<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Car;
use App\Models\Invoice;
use App\Models\Penalty;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class DashboardMonthlySummary extends BaseWidget
{

   protected function getStats(): array
    {
        $today = Carbon::today();
        $returnsToday = Booking::whereDate('tanggal_kembali', $today)->count();

        // Pemesanan mulai hari ini
        $bookingsToday = Booking::whereDate('tanggal_keluar', $today)->count();

        // Mobil dengan status "Ready"
        $carsAvailable = Car::where('status', 'Ready')->count();
        $mostBookedCar = Car::withCount('bookings')
            ->orderByDesc('bookings_count')
            ->first();

        // Mobil dengan status "Disewa"
        $carsRented = Car::where('status', 'Disewa')->count();
        // $invoiceCount = Invoice::where('status', 'Belum Lunas')->count();


        return [
            Stat::make('Pemesanan Hari Ini', $bookingsToday)
                ->description('Pemesanan yang dimulai hari ini')
                ->icon('heroicon-o-calendar')
                ->color('primary'),
            Stat::make('Mobil Tersedia', $carsAvailable)
                ->description('Status Ready')
                ->icon('heroicon-o-check-circle')
                ->color('success'),
            Stat::make('Mobil Disewa', $carsRented)
                ->description("Jumlah Booking Bulan Ini")
                ->icon('heroicon-o-truck')
                ->color('primary'),
            Stat::make('Mobil Kembali Hari Ini', $returnsToday)
                ->description('Jumlah mobil yang kembali hari ini')
                ->icon('heroicon-o-bell-alert')
                ->color($returnsToday > 0 ? 'warning' : 'gray'),






        ];
    }
}
