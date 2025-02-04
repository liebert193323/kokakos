<?php

namespace App\Filament\Resources\ComplaintHandlerResource\Pages;

use App\Filament\Resources\ComplaintHandlerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintHandler extends EditRecord
{
    protected static string $resource = ComplaintHandlerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
