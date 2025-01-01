<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Pembayaran';
    protected static ?string $pluralModelLabel = 'Pembayaran';
    
    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->relationship('tenant', 'name')
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('bill_id')
                    ->relationship('bill', 'description')
                    ->required()
                    ->disabled(),

                Forms\Components\TextInput::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->required()
                    ->numeric()
                    ->disabled()
                    ->prefix('Rp'),

                Forms\Components\Select::make('payment_category')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ])
                    ->required()
                    ->disabled(),

                Forms\Components\DateTimePicker::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->required()
                    ->default(now())
                    ->disabled(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->label('Penyewa')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('bill.description')
                    ->label('Deskripsi Tagihan')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Jumlah Pembayaran')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_category')
                    ->label('Tipe Pembayaran')
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('payment_category')
                    ->label('Tipe Pembayaran')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ]),
                
                Filter::make('payment_date')
                    ->form([
                        Forms\Components\DatePicker::make('payment_date_from')
                            ->label('Dari Tanggal'),
                        Forms\Components\DatePicker::make('payment_date_until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['payment_date_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['payment_date_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('pay')
                    ->label('Bayar')
                    ->action(function (Payment $record) {
                        try {
                            if ($record->bill) {
                                $record->bill->update(['status' => 'paid']);
                            }

                            Notification::make()
                                ->title('Pembayaran Berhasil')
                                ->success()
                                ->send();

                        } catch (\Exception $e) {
                            Log::error('Failed to update bill status:', [
                                'error' => $e->getMessage(),
                                'payment_id' => $record->id,
                                'bill_id' => $record->bill_id
                            ]);

                            Notification::make()
                                ->title('Gagal Memproses Pembayaran')
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
                    ->modalSubheading('Apakah Anda yakin ingin memproses pembayaran ini?')
                    ->visible(fn (Payment $record): bool => 
                        $record->bill && 
                        $record->bill->status === 'unpaid' && 
                        $record->bill->tenant_id === $record->tenant_id
                    ),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        // Menghitung jumlah tagihan yang belum dibayar
        return static::getModel()::whereHas('bill', function ($query) {
            $query->where('status', 'unpaid');
        })->count();
    }
}