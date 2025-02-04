<?php

namespace App\Filament\Resources\BillResource\Pages;

use App\Filament\Resources\BillResource;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Payment;

class CreateBill extends CreateRecord
{
    protected static string $resource = BillResource::class;

    protected function afterCreate(): void
    {
        // Buat payment record otomatis
        Payment::create([
            'tenant_id' => $this->record->tenant_id,
            'room_number' => $this->record->room_id ?? null,
            'bill_id' => $this->record->id,
            'amount' => $this->record->amount,
            'payment_category' => $this->record->payment_category,
            'payment_date' => now(),
        ]);
    }
}