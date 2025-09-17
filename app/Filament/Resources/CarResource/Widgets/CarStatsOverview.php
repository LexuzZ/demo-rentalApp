<?php

namespace App\Filament\Resources\CarResource\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CarStatsOverview extends BaseWidget
{
    public ?\App\Models\Car $record = null;

    protected function getStats(): array
    {
        // Ambil semua booking untuk mobil ini (kecuali yang batal)
        $bookings = Booking::where('car_id', $this->record->id)
            ->where('status', '!=', 'Batal')
            ->get();

        $totalDays = $bookings->sum(function ($booking) {
            return $booking->tanggal_keluar && $booking->tanggal_kembali
                ? Carbon::parse($booking->tanggal_keluar)->diffInDays(Carbon::parse($booking->tanggal_kembali))
                : 0;
        });

        // Hitung total pendapatan dari estimasi biaya
        $totalRevenue = $bookings->sum('estimasi_biaya');

        return [
            Stat::make('Total Hari Disewa', $totalDays . ' hari')
                ->description('Jumlah hari dari semua pemakaian')
                ->icon('heroicon-o-calendar-days')
                ->color('success'),

            Stat::make('Pendapatan Total', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description('Total estimasi dari booking')
                ->icon('heroicon-o-currency-dollar')
                ->color('warning'),
        ];
    }
}
