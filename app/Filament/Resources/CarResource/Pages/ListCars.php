<?php

namespace App\Filament\Resources\CarResource\Pages;

use App\Filament\Resources\CarResource;
use App\View\Components\CarRentalStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCars extends ListRecords
{
    protected static string $resource = CarResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Mobil')
                ->icon('heroicon-o-plus')
                ->color('success'),
        ];
    }

}
