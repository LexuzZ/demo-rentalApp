<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\InvoiceResource;
use App\Models\Booking;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    public function mount(): void
    {
        // Cek apakah ada 'booking_id' di URL
      if (request()->has('booking_id')) {
            $bookingId = request('booking_id');
            $booking = Booking::find($bookingId);

            if ($booking) {
                $estimasi = $booking->estimasi_biaya ?? 0;
                // Asumsi biaya pickup/dropoff defaultnya 0 saat form pertama kali dimuat
                $pickup = 0;
                $total = $estimasi + $pickup;

                // Isi semua field yang relevan di form dengan nilai awal
                $this->form->fill([
                    'booking_id' => $bookingId,
                    'total' => $total,
                    'dp' => 0, // Mengatur DP awal menjadi 0
                    'sisa_pembayaran' => $total, // Sisa pembayaran sama dengan total
                ]);
            }
        }
    }
    // protected function getRedirectUrl(): string
    // {
    //     // Ambil data invoice yang baru saja dibuat
    //     $invoice = $this->getRecord();

    //     // Arahkan kembali ke halaman 'view' dari booking yang berelasi
    //     return BookingResource::getUrl('view', ['record' => $invoice->booking_id]);
    // }
}
