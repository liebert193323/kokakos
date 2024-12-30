<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Models\Payment;
use App\Models\Tenant;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('tenant_id')
                    ->label('Tenant')
                    ->options(Tenant::pluck('name', 'id'))
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $tenant = Tenant::find($state);
                            if ($tenant) {
                                $amount = $tenant->per_month ? 
                                    $tenant->price_per_semester : 
                                    $tenant->price_per_year;

                                $payment_category = $tenant->per_month ? 'semester' : 'year';

                                $set('amount', $amount);
                                $set('payment_category', $payment_category);
                            }
                        }
                    }),

                Forms\Components\TextInput::make('description')
                    ->required()
                    ->maxLength(255),

                Forms\Components\TextInput::make('amount')
                    ->required()
                    ->disabled()
                    ->numeric()
                    ->default(0),

                Forms\Components\Select::make('payment_category')
                    ->options([
                        'semester' => 'Per Semester',
                        'year' => 'Per Tahun',
                    ])
                    ->required()
                    ->disabled(),

                Forms\Components\Select::make('status')
                    ->options([
                        'unpaid' => 'Belum Dibayar',
                        'paid' => 'Sudah Dibayar',
                    ])
                    ->default('unpaid')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('tenant.name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->searchable(),

                Tables\Columns\TextColumn::make('amount')
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
                    ->dateTime()
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('pay')
                    ->label('Bayar')
                    ->action(function (Bill $record) {
                        // Validasi data sebelum membuat pembayaran
                        if (!$record->tenant_id || !$record->amount) {
                            throw new \Exception('Tenant atau jumlah pembayaran tidak valid.');
                        }

                        // Buat pembayaran baru di tabel payments
                        Payment::create([
                            'tenant_id' => $record->tenant_id,
                            'room_id' => $record->room_id, // Pastikan room_id diisi jika diperlukan
                            'bill_id' => $record->id,      // Simpan ID Bill
                            'amount_paid' => $record->amount,
                            'payment_type' => $record->payment_category,
                            'payment_date' => now(),
                        ]);

                        // Perbarui status tagihan menjadi "Sudah Dibayar"
                        $record->update(['status' => 'paid']);
                    })
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalSubheading('Apakah Anda yakin ingin menandai tagihan ini sebagai "Sudah Dibayar"?'),
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
}
