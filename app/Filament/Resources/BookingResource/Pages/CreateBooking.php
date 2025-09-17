<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateBooking extends CreateRecord
{
    protected static string $resource = BookingResource::class;
    protected static ?string $title = 'Tambah Pemesanan';
    // protected static ?string $label = 'Tambah Pemesanan';

    protected function afterCreate(): void
    {
        if ($this->record->status === 'booking' || $this->record->status === 'disewa') {
            $this->record->car->update([
                'status' => 'disewa',
            ]);
        }
    }
}
