<?php

namespace App\Filament\Resources\PenaltyResource\Pages;

use App\Filament\Resources\PenaltyResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePenalty extends CreateRecord
{
    protected static string $resource = PenaltyResource::class;
    public function mount(): void
    {
        // Cek apakah ada 'booking_id' di URL
        if (request()->has('booking_id')) {
            // Isi field 'booking_id' di form dengan nilai dari URL
            $this->form->fill([
                'booking_id' => request('booking_id'),
            ]);
        }
    }
}
