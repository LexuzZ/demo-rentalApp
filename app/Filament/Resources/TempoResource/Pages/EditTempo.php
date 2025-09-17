<?php

namespace App\Filament\Resources\TempoResource\Pages;

use App\Filament\Resources\TempoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTempo extends EditRecord
{
    protected static string $resource = TempoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
