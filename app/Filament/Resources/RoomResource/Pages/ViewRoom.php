<?php

namespace App\Filament\Resources\RoomResource\Pages;

use App\Filament\Resources\RoomResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;

class ViewRoom extends ViewRecord
{
    protected static string $resource = RoomResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return static::$resource::productInfolist($infolist);
    }
}
