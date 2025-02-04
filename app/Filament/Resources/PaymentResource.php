<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use Illuminate\Support\Facades\Log;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\Filter;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Select;
use Exception;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;
    protected static ?string $navigationIcon = 'heroicon-o-credit-card';
    protected static ?string $navigationGroup = 'Keuangan';
    protected static ?string $modelLabel = 'Pembayaran';
    protected static ?string $pluralModelLabel = 'Pembayaran';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Select::make('user_id')
                ->relationship('user', 'name')
                ->label('Pengguna')
                
                ->required()
                ->disabled(),

            Forms\Components\TextInput::make('room_number')
                ->label('Nomor Kamar')
                ->default(fn($record) => $record->bill->room_number ?? '-')
                ->disabled(),

                Forms\Components\Select::make('bill_id')
                ->relationship('bill', 'description')
                ->required()
                ->reactive()
                ->afterStateUpdated(fn ($state, callable $set) => 
                    $set('user_id', \App\Models\Bill::find($state)?->user_id ?? null)
        ),
            
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

            Forms\Components\Select::make('status')
                ->label('Status')
                ->options([
                    'unpaid' => 'Belum Dibayar',
                    'paid' => 'Sudah Dibayar',
                ])
                ->disabled(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('payment_date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Pengguna')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('room_number')
                    ->label('Nomor Kamar')
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
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('payment_date')
                    ->label('Tanggal Pembayaran')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),

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
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '>=', $date),
                            )
                            ->when(
                                $data['payment_date_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('payment_date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\Action::make('bayar')
                ->label('Bayar')
                ->color('success')
                ->icon('heroicon-o-credit-card')
                ->requiresConfirmation()
                ->modalHeading('Konfirmasi Pembayaran')
                ->modalDescription('Apakah Anda yakin ingin memproses pembayaran ini?')
                ->modalSubmitActionLabel('Bayar Sekarang')
                ->action(function (Payment $record): void {
                    try {
                        if ($record->status === 'paid') {
                            throw new Exception('Pembayaran ini sudah dilakukan.');
                        }
            
                        // Update status pembayaran menjadi "Sudah Dibayar"
                        $record->status = 'paid';
                        $record->save();
            
                        // Update status tagihan juga
                        if ($record->bill) {
                            $record->bill->status = 'paid';
                            $record->bill->save();
                        }
            
                        // **Tambahkan logika untuk membuat entri di tabel Income**
                        \App\Models\Income::create([
                            'user_id' => $record->user_id ?? ($record->bill ? $record->bill->user_id : null), // Pastikan user_id tidak null
                            'payment_id' => $record->id,
                            'amount' => $record->amount,
                            'type' => $record->payment_category,
                            'date' => $record->payment_date,
                            'description' => "Pembayaran dari tagihan #" . ($record->bill_id ?? 'Tanpa Tagihan'),
                        ]);
                        
                        
                        
            
                        // Clear cache jika diperlukan
                        cache()->forget('payment_count');
            
                        // Kirim notifikasi ke pengguna
                        Notification::make()
                            ->title('Pembayaran Berhasil')
                            ->body('Pembayaran telah berhasil dan masuk ke pendapatan.')
                            ->success()
                            ->send();
            
                    } catch (Exception $e) {
                        Log::error('Payment processing failed:', [
                            'error' => $e->getMessage(),
                            'payment_id' => $record->id,
                        ]);
            
                        Notification::make()
                            ->title('Gagal Memproses Pembayaran')
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                })            
                    ->visible(
                        fn(Payment $record): bool =>
                        $record->status === 'unpaid' &&
                            ($record->bill?->status === 'unpaid' || $record->bill === null)
                    )
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
        return static::getModel()::where('status', 'unpaid')->count();
    }
}
