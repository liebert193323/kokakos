<?php
namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Payment;
use App\Models\Tenant;
use App\Models\Room;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
            Tables\Columns\TextColumn::make('tenant.name')->label('Tenant'),  // Menampilkan nama tenant
            Tables\Columns\TextColumn::make('room.name')->label('Room'),  // Menampilkan nama room
            Tables\Columns\TextColumn::make('bill.name')->label('Bill'),  // Menampilkan nama bill
            Tables\Columns\TextColumn::make('amount_paid')->label('Amount')->money('IDR'),
            Tables\Columns\TextColumn::make('payment_type')->label('Payment Type'),
            Tables\Columns\TextColumn::make('status')->label('Status'),
            Tables\Columns\TextColumn::make('created_at')->label('Created At')->dateTime(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'pending' => 'Pending',
                    'paid' => 'Paid',
                ]),
            ]);
    }    

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }
}
