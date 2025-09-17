<x-filament-widgets::widget>
    {{-- Cek apakah ada data untuk hari ini --}}
    @if ($bookingsToday->isNotEmpty())
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Mobil Keluar Hari Ini</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($bookingsToday as $record)
                    {{-- Menyertakan view kartu yang sudah ada --}}
                    @include('filament.widgets.components.booking-card', ['record' => $record, 'theme' => 'danger'])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Cek apakah ada data untuk besok --}}
    @if ($bookingsTomorrow->isNotEmpty())
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4 pt-4">Mobil Keluar Besok</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach ($bookingsTomorrow as $record)
                    {{-- Menyertakan view kartu yang sudah ada --}}
                    @include('filament.widgets.components.booking-card', ['record' => $record, 'theme' => 'info'])
                @endforeach
            </div>
        </div>
    @endif

    {{-- Tampilan jika tidak ada data sama sekali --}}
    @if ($bookingsToday->isEmpty() && $bookingsTomorrow->isEmpty())
        <div class="col-span-full text-center text-gray-500 dark:text-gray-400">
            Tidak ada mobil yang dijadwalkan keluar untuk hari ini dan besok.
        </div>
    @endif
</x-filament-widgets::widget>
