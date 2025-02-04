<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\User;
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
                Forms\Components\Select::make('user_id')
                    ->label('Penyewa')
                    ->options(
                        User::whereHas('roles', function ($query) {
                            $query->where('name', 'Penghuni');
                        })->pluck('name', 'id')
                    )
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $user = User::with('rooms')->find($state);
                            if ($user && $user->rooms->isNotEmpty()) {
                                $room = $user->rooms->first(); // Ambil kamar pertama yang dimiliki user
                                $amount = $user->per_month ? $user->price_per_semester : $user->price_per_year;
                                $payment_category = $user->per_month ? 'semester' : 'year';
    
                                $set('amount', $amount);
                                $set('payment_category', $payment_category);
                                $set('room_number', $room->number);
                                $set('room_id', $room->id);
                            }
                        }
                    }),
    
                Forms\Components\TextInput::make('room_number')
                    ->label('Nomor Kamar')
                    ->disabled(),
    
                Forms\Components\Hidden::make('room_id'),
    
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
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penyewa')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('Nomor Kamar')
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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => $state,
                    }),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                        default => $state,
                    })
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
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

            // Debugging: Periksa data yang akan disimpan
            dd([
                'user_id' => $record->user_id,
                'room_id' => $record->room_id,
                'bill_id' => $record->id,
                'amount' => $record->amount,
                'payment_category' => $record->payment_category,
            ]);

            // Buat Payment baru
            Payment::create([
                'user_id' => $record->user_id,
                'room_number' => $record->room_number,
                'bill_id' => $record->id,
                'amount' => $record->amount,
                'payment_category' => $record->payment_category,
                'payment_date' => now(),
                'status' => 'unpaid',
            ]);

            // Perbarui status Bill menjadi "paid"
            $record->update(['status' => 'paid']);

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
                    ->visible(fn(Bill $record): bool => $record->status === 'unpaid'),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->before(function ($records) {
                        foreach ($records as $record) {
                            // Hapus payments sebelum menghapus bills
                            $record->payments()->delete();
                        }
                    })
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
}
