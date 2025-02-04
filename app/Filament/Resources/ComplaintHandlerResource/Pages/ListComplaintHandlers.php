<?php

namespace App\Filament\Resources\ComplaintHandlerResource\Pages;

use App\Filament\Resources\ComplaintHandlerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListComplaintHandlers extends ListRecords
{
    protected static string $resource = ComplaintHandlerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
