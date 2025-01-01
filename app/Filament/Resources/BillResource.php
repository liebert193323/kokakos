<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Room;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Notifications\Notification;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;
    protected static ?string $navigationLabel = 'Tagihan';
    protected static ?string $pluralLabel = 'Tagihan';
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Keuangan';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Penyewa')
                    ->options(Tenant::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $tenant = Tenant::with('room')->find($state);
                            if ($tenant) {
                                $amount = $tenant->per_month ? 
                                    $tenant->price_per_semester : 
                                    $tenant->price_per_year;
                    
                                $payment_category = $tenant->per_month ? 'semester' : 'year';
                    
                                // Tambahkan log untuk debugging
                                logger()->info('Tenant room_id:', [
                                    'tenant_id' => $tenant->id,
                                    'room_id' => $tenant->room_id
                                ]);
                    
                                $set('amount', $amount);
                                $set('payment_category', $payment_category);
                                $set('room_id', $tenant->room_id); // Pastikan room_id ada nilainya
                    
                                // Force refresh form
                                $set('room_id', null);
                                $set('room_id', $tenant->room_id);
                            }
                        }
                    }),

                

                Forms\Components\TextInput::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah')
                    ->required()
                    ->disabled()
                    ->numeric(),

                Forms\Components\Select::make('payment_category')
                    ->label('Kategori Pembayaran')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ])
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                    ])
                    ->default('unpaid')
                    ->disabled()
                    ->dehydrated(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('room.name')
                    ->label('Kamar')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->label('Deskripsi')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_category')
                    ->label('Kategori')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'unpaid' => 'danger',
                        'paid' => 'success',
                        default => 'primary',
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make(),
                
                Tables\Actions\Action::make('pay')
    ->label('Bayar')
    ->action(function (Bill $record) {
        try {
            if ($record->status === 'paid') {
                throw new \Exception('Tagihan ini sudah dibayar.');
            }

            // Buat payment record
            Payment::create([
                'tenant_id' => $record->tenant_id,
                'room_id' => $record->room_id,
                'bill_id' => $record->id,
                'amount' => $record->amount,
                'payment_type' => $record->payment_category,
                'payment_date' => now(),
            ]);

            // Update status bill
            $record->update(['status' => 'paid']);

            // Kurangi badge counter setelah pembayaran berhasil
            cache()->forget('payment_count');

            Notification::make()
                ->title('Pembayaran Berhasil')
                ->success()
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Pembayaran Gagal')
                ->body($e->getMessage())
                ->danger()
                ->send();

            throw $e;
        }
    })
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription('Apakah Anda yakin ingin menandai tagihan ini sebagai "Sudah Dibayar"?')
                    ->visible(fn (Bill $record): bool => $record->status === 'unpaid'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            'create' => Pages\CreateBill::route('/create'),
            'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
};