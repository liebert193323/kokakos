<?php

namespace App\Filament\Resources\ComplaintManagerResource\Pages;

use App\Filament\Resources\ComplaintManagerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditComplaintManager extends EditRecord
{
    protected static string $resource = ComplaintManagerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
