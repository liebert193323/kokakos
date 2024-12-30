<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\Tenant;
use App\Models\Complaint;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();

        return [
            // Kamar Terpakai
            Stat::make('Kamar Terpakai', $occupiedRooms)
                ->description("$totalRooms kamar")
                ->url(route('filament.kokakos.resources.rooms.index')),

            // Kamar Tersedia
            Stat::make('Kamar Tersedia', $availableRooms)
                ->description("$totalRooms kamar")
                ->url(route('filament.kokakos.resources.rooms.index')),

            // Total Penghuni
            Stat::make('Total Penghuni', Tenant::count())
                ->url(route('filament.kokakos.resources.tenants.index')),

            // Total Pemasukan (contoh: harga total per tahun)
            Stat::make('Total Pemasukan', 'Rp ' . number_format(Room::sum('price_per_year'), 0, ',', '.'))
                ->url(route('filament.kokakos.resources.incomes.index')),

            // Total Pengaduan
            Stat::make('Total Pengaduan', Complaint::count())
                ->url(route('filament.kokakos.resources.complaints.index')),
        ];
    }
}
