<?php

namespace App\Filament\Widgets;

use App\Models\Tempo;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;

class TempoDueToday extends Widget
{
    // Arahkan ke file Blade yang akan kita modifikasi
    protected static string $view = 'filament.widgets.tempo-due-today-card';

    // Ubah judul widget agar lebih sesuai
    protected static ?string $heading = 'Jadwal Perawatan Mendatang';

    protected int|string|array $columnSpan = 'full';

    // Method untuk mengambil dan mengirim data ke view
    public function selesaikanTempo(int $tempoId): void
    {
        $tempo = Tempo::find($tempoId);

        if ($tempo) {
            // Hapus data tempo dari database
            $tempo->delete();

            // Kirim notifikasi sukses
            Notification::make()
                ->title('Jadwal Selesai')
                ->body('Jadwal perawatan telah ditandai sebagai selesai dan dihapus dari daftar.')
                ->success()
                ->send();
        }
    }
   protected function getViewData(): array
{
    $tempos = Tempo::query()
        ->with(['car.carModel.brand'])
        // Mengambil data antara hari ini dan 1 bulan ke depan
        ->whereBetween('jatuh_tempo', [today(), today()->addMonth()])
        ->orderBy('jatuh_tempo', 'asc')
        ->get();

    return [
        'tempos' => $tempos,
    ];
}
}
