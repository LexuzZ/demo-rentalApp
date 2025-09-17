@php
    // PERBAIKAN 1: Menentukan warna dinamis untuk tombol dan badge
    $badgeColor = match ($theme ?? 'default') {
        'danger' => '#ef4444', // Merah untuk hari ini
        'info'   => '#10b981', // Hijau untuk besok
        default  => '#6b7280;', // Abu-abu sebagai default
    };
    $buttonColor = match ($theme ?? 'default') {
        'danger' => 'danger',
        'info'   => 'success',
        default  => 'gray',
    };
@endphp

{{-- Menghapus style latar belakang dari div utama --}}
<div class="p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
    {{-- Header Kartu --}}
    <div class="flex items-start justify-between">
        <div>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                {{ $record->car->carModel->brand->name }} {{ $record->car->carModel->name }}
            </h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">
                {{ $record->car->nopol }}
            </p>
        </div>
        {{-- Badge Status --}}
        @php
            $status = $record->status;
            $statusText = match ($status) {
                'disewa' => 'Disewa',
                default => ucfirst($status),
            };
        @endphp
        {{-- PERBAIKAN 2: Menerapkan warna dinamis pada badge --}}
        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full text-white" style="background-color: {{ $badgeColor }};">
            {{ $statusText }}
        </span>
    </div>

    <hr class="my-4 border-gray-200 dark:border-gray-700">

    {{-- Detail Konten --}}
    <div class="space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Penyewa</span>
            <span class="font-medium text-gray-900 dark:text-white text-xs">{{ $record->customer->nama ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Vendor</span>
            <span class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->car->garasi ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Staff</span>
            <span class="font-medium text-gray-900 dark:text-white text-xs">
                {{ $record->driver->nama ?? 'N/A' }}
            </span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Lokasi Pengembalian</span>
            <span
                class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->lokasi_pengembalian ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Waktu Kembali</span>
            <div class="text-right">
                <p class="font-semibold text-xs">
                    Pukul {{ \Carbon\Carbon::parse($record->waktu_kembali)->locale('id')->format('H:i') }} WITA
                </p>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Tanggal Kembali</span>
            <div class="text-right">
                <p class="font-semibold text-xs">
                    {{ \Carbon\Carbon::parse($record->tanggal_kembali)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="mt-6 flex items-center justify-end">
        {{-- PERBAIKAN 3: Menerapkan warna dinamis pada tombol --}}
        <x-filament::button wire:click="selesaikanBooking({{ $record->id }})" wire:loading.attr="disabled"
            icon="heroicon-o-check-circle" color="{{ $buttonColor }}">
            Selesaikan
        </x-filament::button>
    </div>
</div>
