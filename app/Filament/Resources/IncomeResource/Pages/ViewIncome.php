<?php

namespace App\Filament\Resources\IncomeResource\Pages;

use App\Filament\Resources\IncomeResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action; // Update namespace
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewIncome extends ViewRecord
{
    protected static string $resource = IncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Kembali')
                ->url(IncomeResource::getUrl())
                ->color('secondary')
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pendapatan')
                    ->schema([
                        Infolists\Components\TextEntry::make('tenant.name')
                            ->label('Penyewa'),

                        Infolists\Components\TextEntry::make('payment.id')
                            ->label('ID Pembayaran'),

                        Infolists\Components\TextEntry::make('amount')
                            ->label('Jumlah')
                            ->money('IDR'),

                        Infolists\Components\TextEntry::make('type')
                            ->label('Tipe')
                            ->formatStateUsing(fn (string $state): string => match ($state) {
                                'semester' => 'Per Semester',
                                'year' => 'Per Tahun',
                                default => $state,
                            }),

                        Infolists\Components\TextEntry::make('date')
                            ->label('Tanggal')
                            ->date('d/m/Y'),

                        Infolists\Components\TextEntry::make('description')
                            ->label('Keterangan'),
                    ])
                    ->columns(2),
            ]);
    }
}