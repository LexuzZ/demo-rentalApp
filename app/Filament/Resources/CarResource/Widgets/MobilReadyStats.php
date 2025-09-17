<?php

namespace App\Filament\Resources\CarResource\Widgets;

use App\Models\Car;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class MobilReadyStats extends BaseWidget
{
    protected function getStats(): array
    {
        $cars = Car::where('status', 'ready')
            ->select('nama_mobil')
            ->selectRaw('count(*) as total')
            ->groupBy('nama_mobil')
            ->get();

        // Gabungkan hasil menjadi 1 string multiline
        $output = $cars->map(fn ($car) => "{$car->nama_mobil}: {$car->total} unit")->implode("\n");

        return [
            Stat::make('Mobil Tersedia', '')
                ->description(nl2br($output)) // Menampilkan multiline di UI
                ->descriptionIcon('heroicon-o-truck')
        ];
    }
}
