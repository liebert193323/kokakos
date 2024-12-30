<?php
namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use App\Models\Bill; // Pastikan model Bill di-import
use App\Models\Payment; // Pastikan model Payment di-import

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    // Menambahkan kolom 'amount_due' untuk menampilkan tagihan yang harus dibayar
    protected function getTableColumns(): array
    {
        return [
            \Filament\Tables\Columns\TextColumn::make('id')
                ->label('Payment ID')
                ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('bill.name')  // Menampilkan nama Bill terkait
                ->label('Bill Name')
                ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('bill.description')  // Menampilkan deskripsi Bill
                ->label('Bill Description'),
            \Filament\Tables\Columns\TextColumn::make('amount')  // Menampilkan jumlah pembayaran
                ->label('Payment Amount')
                ->sortable(),
            \Filament\Tables\Columns\TextColumn::make('status')  // Menampilkan status pembayaran
                ->label('Payment Status'),
            \Filament\Tables\Columns\TextColumn::make('bill.amount')  // Menampilkan jumlah tagihan yang harus dibayar
                ->label('Amount Due')
                ->getStateUsing(function (Payment $record) {
                    return $record->bill ? $record->bill->amount : 'N/A';
                }),
            \Filament\Tables\Columns\TextColumn::make('bill.per_month')  // Menampilkan kategori per bulan/per tahun
                ->label('Category')
                ->getStateUsing(function (Payment $record) {
                    return $record->bill ? $record->bill->per_month : 'N/A';
                }),
        ];
    }
}
