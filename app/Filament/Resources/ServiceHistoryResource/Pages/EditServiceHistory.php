<?php

namespace App\Filament\Resources\ServiceHistoryResource\Pages;

use App\Filament\Resources\ServiceHistoryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditServiceHistory extends EditRecord
{
    protected static string $resource = ServiceHistoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
