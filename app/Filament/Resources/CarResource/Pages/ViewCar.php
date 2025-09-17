<?php

namespace App\Filament\Resources\CarResource\Pages;

use App\Filament\Resources\CarResource;
use Filament\Resources\Pages\ViewRecord;

class ViewCar extends ViewRecord
{
    protected static string $resource = CarResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Resources\CarResource\Widgets\CarStatsOverview::class,
        ];
    }
}
