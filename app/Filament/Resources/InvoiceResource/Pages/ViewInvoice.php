<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Edit akan otomatis muncul di sini jika pengguna memiliki izin
            Actions\EditAction::make(),
            Actions\Action::make('kembali_ke_booking')
                ->label('Detail Pesanan')
                ->icon('heroicon-o-arrow-left')
                ->url(function () {
                    $booking = $this->record->booking;
                    if ($booking) {
                        // redirect ke halaman view booking di Filament
                        return \App\Filament\Resources\BookingResource::getUrl('view', [
                            'record' => $booking->id,
                        ]);
                    }

                    // fallback kalau tidak ada booking
                    return \App\Filament\Resources\BookingResource::getUrl();
                })
                ->color('gray'),
        ];
    }
}
