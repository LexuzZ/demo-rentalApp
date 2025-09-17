<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\BookingResource\Widgets\BookingStats;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Pesanan') // ubah teks tombol
                // ->label('Tambah Mobil')
                ->icon('heroicon-o-plus')
                ->color('success')

        ];
    }
    // protected function getHeaderWidgets(): array
    // {
    //     return [
    //         BookingStats::class,
    //     ];
    // }
}
