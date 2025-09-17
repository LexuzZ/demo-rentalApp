<?php

namespace App\Filament\Widgets;

use App\Models\Car;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\DB;

class AvailableCarsOverview extends Widget
{
    protected static string $view = 'filament.widgets.available-cars-overview';
    protected int | string | array $columnSpan = 'full';

    public function getViewData(): array
    {
         $availableCars = Car::with(['carModel']) // <-- Eager load relasi
        ->where('status', 'ready')
        ->get();

    // Grouping sekarang dilakukan berdasarkan nama merek dari relasi
    $groupedCars = $availableCars->groupBy('carModel.name');

    return [
        'cars' => $groupedCars,
    ];
    }
}


