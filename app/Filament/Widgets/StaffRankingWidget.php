<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Driver;
use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class StaffRankingWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.widgets.staff-ranking-widget';
    // protected int | string | array $columnSpan = 4;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'selectedDate' => now()->format('Y-m-d'),
        ]);
    }

    /**
     * Definisikan form dan hubungkan ke properti $data.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data'); // <-- KUNCI PERBAIKANNYA DI SINI
    }

    /**
     * Definisikan skema form di sini.
     */
    protected function getFormSchema(): array
    {
        return [
            DatePicker::make('selectedDate')
                ->label('Pilih Tanggal')
                ->maxDate(now())
                ->live(), // ->live() akan otomatis me-refresh widget saat tanggal diubah
        ];
    }

    /**
     * Fungsi utama untuk mengambil, menghitung, dan mengurutkan statistik staff.
     */
    protected function getStats(): Collection
    {
        try {
            // Ambil tanggal dari data form
            $date = Carbon::parse($this->form->getState()['selectedDate']);
        } catch (\Exception $e) {
            $date = now();
        }

        // 1. Ambil semua data penyerahan pada tanggal yang dipilih
        $penyerahan = Booking::whereDate('tanggal_keluar', $date)
            ->whereNotNull('driver_id')
            ->get()
            ->groupBy('driver_id');

        // 2. Ambil semua data pengembalian pada tanggal yang dipilih
        $pengembalian = Booking::whereDate('tanggal_kembali', $date)
            ->whereNotNull('driver_id')
            ->get()
            ->groupBy('driver_id');

        // 3. Dapatkan semua ID staff yang terlibat
        $involvedDriverIds = $penyerahan->keys()->merge($pengembalian->keys())->unique();

        if ($involvedDriverIds->isEmpty()) {
            return collect();
        }

        // 4. Ambil data staff yang terlibat
        $drivers = Driver::whereIn('id', $involvedDriverIds)->get();

        // 5. Gabungkan data menjadi satu koleksi yang rapi
        $stats = $drivers->map(function ($driver) use ($penyerahan, $pengembalian) {
            $penyerahanCount = $penyerahan->get($driver->id, collect())->count();
            $pengembalianCount = $pengembalian->get($driver->id, collect())->count();

            return [
                'staff_name' => $driver->nama,
                'penyerahan' => $penyerahanCount,
                'pengembalian' => $pengembalianCount,
                'total' => $penyerahanCount + $pengembalianCount,
            ];
        });

        // 6. Urutkan berdasarkan total terbanyak, lalu berdasarkan nama
        return $stats->sortByDesc('total')->values();
    }

    /**
     * Kirim data yang sudah diproses ke file view.
     */
    protected function getViewData(): array
    {
        return [
            'stats' => $this->getStats(),
            'dateForHumans' => Carbon::parse($this->form->getState()['selectedDate'])->locale('id')->isoFormat('D MMMM YYYY'),
        ];
    }
}
