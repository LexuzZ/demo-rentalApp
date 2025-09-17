<?php

// Ganti namespace jika Anda memindahkannya
namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

// Ganti nama kelasnya
class MobilKeluar extends Widget
{
    // Tentukan file view yang akan kita buat
    protected static string $view = 'filament.widgets.mobil-keluar-card';

    // Properti dari widget lama Anda
    protected static ?string $heading = 'Mobil Keluar Hari Ini & Besok';
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public function pickupBooking(int $bookingId): void
    {
        if (! Auth::user()->hasAnyRole(['superadmin', 'admin'])) {
            return;
        }
        $booking = Booking::with('car')->find($bookingId);

        if ($booking) {
            // Ubah status booking menjadi 'disewa'
            $booking->status = 'disewa';
            // Ubah juga status mobil menjadi 'disewa'
            $booking->car->status = 'disewa';

            $booking->save();
            $booking->car->save();

            Notification::make()
                ->title('Mobil Telah Diambil')
                ->body("Booking untuk mobil {$booking->car->nopol} telah diubah menjadi 'Disewa'.")
                ->success()
                ->send();
        }
    }
    // Pindahkan query ke getViewData()
    protected function getViewData(): array
    {
        $today = \Carbon\Carbon::today('Asia/Jakarta');
        $tomorrow = \Carbon\Carbon::tomorrow('Asia/Jakarta');

        // Mengambil data untuk hari ini
        $bookingsToday = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', $today)
            ->orderBy('waktu_keluar')
            ->get();

        // Mengambil data untuk besok
        $bookingsTomorrow = Booking::with(['car.carModel.brand', 'customer', 'driver'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', $tomorrow)
            ->orderBy('waktu_keluar')
            ->get();

        return [
            'bookingsToday' => $bookingsToday,
            'bookingsTomorrow' => $bookingsTomorrow,
            'canPerformActions' => Auth::user()->hasAnyRole(['superadmin', 'admin']),
        ];
    }
}
