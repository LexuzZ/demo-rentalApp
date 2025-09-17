<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Carbon\Carbon;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class OverdueTasksWidget extends Widget
{
    protected static string $view = 'filament.widgets.overdue-tasks-widget';
    protected static ?int $sort = 1;
    protected int|string|array $columnSpan = 'full';

    public function pickupOverdue(int $bookingId): void
    {
        if (! Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor'])) {
            return;
        }

        $booking = Booking::with('car')->find($bookingId);

        if ($booking && $booking->status === 'booking') {
            $booking->status = 'disewa';
            $booking->car->status = 'disewa';
            $booking->save();
            $booking->car->save();

            Notification::make()
                ->title('Pick Up Disetujui')
                ->body("Mobil {$booking->car->nopol} telah diubah menjadi status 'Disewa'.")
                ->success()
                ->send();
        }
    }

    public function returnOverdue(int $bookingId): void
    {
        if (! Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor'])) {
            return;
        }

        $booking = Booking::with('car')->find($bookingId);

        if ($booking && $booking->status === 'disewa') {
            $booking->status = 'selesai';
            $booking->car->status = 'ready';
            $booking->save();
            $booking->car->save();

            Notification::make()
                ->title('Pengembalian Diproses')
                ->body("Mobil {$booking->car->nopol} telah dikembalikan dan tersedia.")
                ->success()
                ->send();
        }
    }

    protected function getViewData(): array
    {
        $today = Carbon::today('Asia/Jakarta');

        $overduePickups = Booking::with(['car.carModel.brand', 'customer'])
            ->where('status', 'booking')
            ->whereDate('tanggal_keluar', '<', $today)
            ->orderBy('tanggal_keluar', 'desc')
            ->get();

        $overdueReturns = Booking::with(['car.carModel.brand', 'customer'])
            ->where('status', 'disewa')
            ->whereDate('tanggal_kembali', '<', $today)
            ->orderBy('tanggal_kembali', 'desc')
            ->get();

        return [
            'overduePickups' => $overduePickups,
            'overdueReturns' => $overdueReturns,
            'canPerformActions' => Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor']),
        ];
    }
}
