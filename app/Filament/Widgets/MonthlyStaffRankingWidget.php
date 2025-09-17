<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Driver;
use Carbon\Carbon;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Widgets\Widget;
use Illuminate\Support\Collection;

class MonthlyStaffRankingWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    // Arahkan ke file view yang baru
    protected static string $view = 'filament.widgets.monthly-staff-ranking-widget';
    protected int|string|array $columnSpan = 'full';
    public ?array $data = [];

    /**
     * Inisialisasi widget dengan bulan dan tahun saat ini.
     */
    public function mount(): void
    {
        $this->form->fill([
            'selectedMonth' => now()->month,
            'selectedYear' => now()->year,
        ]);
    }

    /**
     * Definisikan form dan hubungkan ke properti $data.
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema($this->getFormSchema())
            ->statePath('data');
    }

    /**
     * Definisikan skema form dengan filter bulan dan tahun.
     */
    protected function getFormSchema(): array
    {
        // Membuat daftar tahun, misalnya 5 tahun ke belakang dari sekarang
        $years = range(now()->year, now()->year - 5);

        return [
            Grid::make(2)->schema([
                Select::make('selectedMonth')
                    ->label('Pilih Bulan')
                    ->options([
                        '1' => 'Januari',
                        '2' => 'Februari',
                        '3' => 'Maret',
                        '4' => 'April',
                        '5' => 'Mei',
                        '6' => 'Juni',
                        '7' => 'Juli',
                        '8' => 'Agustus',
                        '9' => 'September',
                        '10' => 'Oktober',
                        '11' => 'November',
                        '12' => 'Desember',
                    ])
                    ->live(), // Memicu refresh saat bulan diubah

                Select::make('selectedYear')
                    ->label('Pilih Tahun')
                    ->options(array_combine($years, $years)) // Membuat array [2025 => 2025, ...]
                    ->live(), // Memicu refresh saat tahun diubah
            ])
        ];
    }

    /**
     * Fungsi utama untuk mengambil statistik staff berdasarkan bulan dan tahun yang dipilih.
     */
    protected function getStats(): Collection
    {
        try {
            $state = $this->form->getState();
            $month = $state['selectedMonth'];
            $year = $state['selectedYear'];
        } catch (\Exception $e) {
            $month = now()->month;
            $year = now()->year;
        }

        // 1. Ambil semua data penyerahan pada bulan dan tahun yang dipilih
        $penyerahan = Booking::whereYear('tanggal_keluar', $year)
            ->whereMonth('tanggal_keluar', $month)
            ->whereNotNull('driver_id')
            ->get()
            ->groupBy('driver_id');

        // 2. Ambil semua data pengembalian pada bulan dan tahun yang dipilih
        $pengembalian = Booking::whereYear('tanggal_kembali', $year)
            ->whereMonth('tanggal_kembali', $month)
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
        $state = $this->form->getState();
        $dateForHumans = Carbon::createFromDate($state['selectedYear'], $state['selectedMonth'], 1)->locale('id')
            ->isoFormat('MMMM YYYY');

        return [
            'stats' => $this->getStats(),
            'dateForHumans' => $dateForHumans,
        ];
    }
}
