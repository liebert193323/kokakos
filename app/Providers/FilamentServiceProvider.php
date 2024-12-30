<?php
use App\Filament\Resources\RoomResource;
use Filament\Facades\Filament;

class FilamentServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Filament::registerResources([
            RoomResource::class,
        ]);
    }
}
