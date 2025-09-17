<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class MobilKembali extends Widget
{
    protected static string $view = 'filament.widgets.mobil-kembali-card';

    protected static ?string $heading = 'Mobil Kembali Hari Ini & Besok';
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    /**
     * Aksi ini akan dipanggil oleh tombol "Selesaikan" pada setiap kartu.
     */
    public function selesaikanBooking(int $bookingId): void
    {
        $booking = Booking::find($bookingId);

        if ($booking) {
            // Ubah status booking menjadi 'selesai'
            $booking->status = 'selesai';
            // Ubah juga status mobil menjadi 'ready'
            $booking->car->status = 'ready';

            $booking->save();
            $booking->car->save();

            Notification::make()
                ->title('Sewa Selesai')
                ->body("Booking untuk mobil {$booking->car->nopol} telah berhasil diselesaikan.")
                ->success()
                ->send();
        }
    }

    /**
     * Mengambil data untuk dikirim ke view.
     */
    protected function getViewData(): array
    {
        $today = \Carbon\Carbon::today('Asia/Jakarta');
        $tomorrow = \Carbon\Carbon::tomorrow('Asia/Jakarta');

        // Mengambil data untuk hari ini
        $bookingsToday = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'disewa')
            ->whereDate('tanggal_kembali', $today)
            ->orderBy('waktu_kembali')
            ->get();

        // Mengambil data untuk besok
        $bookingsTomorrow = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'disewa')
            ->whereDate('tanggal_kembali', $tomorrow)
            ->orderBy('waktu_kembali')
            ->get();

        return [
            'bookingsToday' => $bookingsToday,
            'bookingsTomorrow' => $bookingsTomorrow,
        ];
    }
}
