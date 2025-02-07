<?php

namespace App\Filament\Resources\ComplaintResource\Pages;

use App\Filament\Resources\ComplaintResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewComplaint extends ViewRecord
{
    protected static string $resource = ComplaintResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\TextEntry::make('title')
                    ->label('Judul'),
                Infolists\Components\TextEntry::make('tenant_name')
                    ->label('Nama Penyewa'),
                Infolists\Components\TextEntry::make('room_number')
                    ->label('Nomor Kamar'),
                Infolists\Components\ImageEntry::make('photo')
                    ->label('Foto'),
                Infolists\Components\TextEntry::make('description')
                    ->label('Deskripsi')
                    ->markdown(),
                Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending' => 'Menunggu',
                        'processing' => 'Diproses',
                        'completed' => 'Selesai',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'processing' => 'info',
                        'completed' => 'success',
                        default => 'warning',
                    }),
                Infolists\Components\TextEntry::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime(),
                // Menampilkan response jika ada
                Infolists\Components\TextEntry::make('response')
                    ->label('Tanggapan')
                    ->visible(fn ($record) => !empty($record->response))
                    ->markdown(),
            ]);
    }
}