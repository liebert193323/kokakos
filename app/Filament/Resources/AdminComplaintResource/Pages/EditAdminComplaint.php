<?php

namespace App\Filament\Resources\AdminComplaintResource\Pages;

use App\Filament\Resources\AdminComplaintResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditAdminComplaint extends EditRecord
{
    protected static string $resource = AdminComplaintResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
