<?php

namespace App\Filament\Widgets;

use App\Models\Room;
use App\Models\Complaint;
use App\Models\Income;
use App\Models\Payment;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalRooms = Room::count();
        $occupiedRooms = Room::where('status', 'occupied')->count();
        $availableRooms = Room::where('status', 'available')->count();
        $totalIncome = Income::sum('amount'); // ✅ Total pemasukan
        $totalComplaints = Complaint::count(); // ✅ Total pengaduan masuk
        $resolvedComplaints = Complaint::where('status', 'completed')->count(); // ✅ Total pengaduan selesai

        $user = Auth::user();
        $stats = [];

        // Pastikan user sudah login sebelum memeriksa role
        if ($user && method_exists($user, 'hasRole')) {
            if ($user->hasRole('super_admin')) {
                // ✅ Statistik untuk super_admin
                $stats = [
                    Stat::make('Total Kamar', $totalRooms)
                        ->description('Jumlah total kamar')
                        ->icon('heroicon-o-home')
                        ->color('primary')
                        ->url(route('filament.kokakos.resources.rooms.index')),

                    Stat::make('Kamar Tersedia', $availableRooms)
                        ->description('Jumlah kamar yang tersedia')
                        ->icon('heroicon-o-home')
                        ->color('danger')
                        ->url(route('filament.kokakos.resources.rooms.index')),

                    Stat::make('Kamar Terisi', $occupiedRooms)
                        ->description('Jumlah kamar yang ditempati')
                        ->icon('heroicon-o-user-group')
                        ->color('success')
                        ->url(route('filament.kokakos.resources.rooms.index')),


                    // ✅ Menampilkan total pemasukan
                    Stat::make('Total Pemasukan', 'Rp ' . number_format($totalIncome, 0, ',', '.'))
                        ->description('Total pemasukan dari semua pembayaran')
                        ->icon('heroicon-o-currency-dollar')
                        ->color('success')
                        ->url(route('filament.kokakos.resources.incomes.index')),

                    // ✅ Menampilkan jumlah pengaduan masuk
                    Stat::make('Total Pengaduan Masuk', $totalComplaints)
                        ->description('Jumlah pengaduan yang masuk')
                        ->icon('heroicon-o-chat-bubble-left')
                        ->color('warning'),

                    // ✅ Menampilkan jumlah pengaduan yang sudah selesai
                    Stat::make('Total Pengaduan Selesai', $resolvedComplaints)
                        ->description('Jumlah pengaduan yang telah diselesaikan')
                        ->icon('heroicon-o-check-circle')
                        ->color('success'),

                ];
            } elseif ($user->hasRole('penghuni')) {
                // ✅ Statistik untuk penghuni
                $roomNumber = $user->rooms()->first()?->number ?? 'Belum memiliki kamar';
                $totalPayments = Payment::where('user_id', $user->id)->sum('amount') ?? 0; // ✅ Hanya mengambil pembayaran user login

                $stats = [
                    Stat::make('Nomor Kamar', $roomNumber)
                        ->description('Nomor kamar yang ditempati')
                        ->icon('heroicon-o-home')
                        ->color('info')
                ];
            }
        }

        return $stats;
    }

    // ✅ Widget akan ditampilkan untuk semua role yang login
    public static function canView(): bool
    {
        return Auth::check();
    }
}
