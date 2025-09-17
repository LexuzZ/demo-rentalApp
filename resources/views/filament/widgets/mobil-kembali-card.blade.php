<x-filament-widgets::widget>
    {{-- Cek apakah ada data untuk hari ini --}}
    @if ($bookingsToday->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4 pt-4">Mobil Kembali Hari Ini</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($bookingsToday as $record)
                    {{-- Mengirimkan tema 'danger' untuk kartu hari ini --}}
                    @include('filament.widgets.components.mobil-kembali-booking-card', ['record' => $record, 'theme' => 'danger'])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Cek apakah ada data untuk besok --}}
    @if ($bookingsTomorrow->isNotEmpty())
        <div>
            <h3 class="text-lg font-semibold mb-4 pt-4">Mobil Kembali Besok</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($bookingsTomorrow as $record)
                    {{-- Mengirimkan tema 'info' untuk kartu besok --}}
                    @include('filament.widgets.components.mobil-kembali-booking-card', ['record' => $record, 'theme' => 'info'])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tampilan jika tidak ada data sama sekali --}}
    @if ($bookingsToday->isEmpty() && $bookingsTomorrow->isEmpty())
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400 py-4">
            Tidak ada mobil yang dijadwalkan kembali untuk hari ini dan besok.
        </div>
    @endif
</x-filament-widgets::widget>
