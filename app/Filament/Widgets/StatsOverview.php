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
        return [
            // Total Kamar
            Stat::make('Total Kamar', Room::count())
                ->url(route('filament.kokakos.resources.rooms.index')), // Menuju halaman daftar kamar

            // Total Penghuni
            Stat::make('Total Penghuni', Tenant::count())
                ->url(route('filament.kokakos.resources.tenants.index')), // Menuju halaman daftar penghuni

            // Total Pemasukan (contoh: harga total per tahun)
            Stat::make('Total Pemasukan', 'Rp ' . number_format(Room::sum('price_per_year'), 0, ',', '.'))
                ->url(route('filament.kokakos.resources.incomes.index')), // Menuju halaman daftar pemasukan (atau buat halaman khusus)

            // Total Pengaduan
            Stat::make('Total Pengaduan', Complaint::count())
                ->url(route('filament.kokakos.resources.complaints.index')), // Menuju halaman daftar pengaduan
        ];
    }
}
