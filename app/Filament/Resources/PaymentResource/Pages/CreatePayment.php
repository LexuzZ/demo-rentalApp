<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\BookingResource;
use App\Filament\Resources\PaymentResource;
use App\Models\Invoice;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;
    public function mount(): void
    {
        // Cek apakah ada 'booking_id' di URL
        if (request()->has('invoice_id')) {
            $invoiceId = request('invoice_id');
            $invoice = Invoice::with('booking.penalty')->find($invoiceId);

            if ($invoice) {
                $totalInvoice = $invoice->total;
                $totalDenda = $invoice->booking?->penalty->sum('amount') ?? 0;

                $this->form->fill([
                    'invoice_id' => $invoiceId,
                    'pembayaran' => $totalInvoice + $totalDenda,
                ]);
            }
        }
    }
     protected function getRedirectUrl(): string
    {
        // Ambil data pembayaran yang baru saja dibuat
        $payment = $this->getRecord();

        // Arahkan kembali ke halaman 'view' dari booking yang berelasi
        // melalui relasi invoice
        return BookingResource::getUrl('view', ['record' => $payment->invoice->booking_id]);
    }
}
