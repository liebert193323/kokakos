<?php
// App/Filament/Resources/RoomResource/Pages/ListRooms.php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;

class ListRooms extends ListRecords
{
    protected static string $resource = RoomResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Kamar Baru'),
        ];
    }
}
