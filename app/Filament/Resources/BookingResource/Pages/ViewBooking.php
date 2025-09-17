<?php

namespace App\Filament\Resources\BookingResource\Pages;

use App\Filament\Resources\BookingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBooking extends ViewRecord
{
    protected static string $resource = BookingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Tombol Edit akan otomatis muncul di sini jika pengguna memiliki izin
            Actions\EditAction::make()->label('Ubah')
                ->icon('heroicon-o-pencil')
                ->color('warning')
                ->button(),
            Actions\DeleteAction::make()->label('Hapus')
                ->icon('heroicon-o-trash')
                ->color('danger'),
            Actions\Action::make('edit_invoice')
                ->label('Faktur')
                ->icon('heroicon-o-eye')   // 👁 ikon mata
                ->color('info')            // biru → konsisten dengan "lihat"
                ->button()
                ->url(function () {
                    $invoice = $this->record->invoice;
                    if ($invoice) {
                        return \App\Filament\Resources\InvoiceResource::getUrl('view', [
                            'record' => $invoice->id,
                        ]);
                    }

                    // fallback kalau belum ada invoice
                    return \App\Filament\Resources\InvoiceResource::getUrl();
                })
                ->visible(fn() => $this->record->invoice !== null),

        ];
    }
}
