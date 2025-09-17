@php
    // PERBAIKAN 1: Menentukan warna dinamis untuk tombol dan badge
    $badgeColor = match ($theme ?? 'default') {
        'danger' => '#8AA624', // Merah untuk hari ini
        'info' => '#005b8f', // Hijau untuk besok
        default => '#6b7280;', // Abu-abu sebagai default
    };
    $buttonColor = match ($theme ?? 'default') {
        'danger' => '#8AA624', // Merah untuk hari ini
        'info' => '#005b8f', // Hijau untuk besok
        default => '#6b7280;', // Abu-abu sebagai default
    };
    $buttonHover = match ($theme ?? 'default') {
        'danger' => '#117554',
        'info' => '#1C6EA4',
        default => '#6b7280;',
    };
@endphp
<div class="bg-white p-6 rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
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
                'booking' => 'Booking',
                default => ucfirst($status),
            };
        @endphp
        <span class="text-xs font-medium px-2.5 py-0.5 rounded-full bg-primary-500 text-white"
            style="background-color: {{ $badgeColor }};">
            {{ $statusText }}
        </span>
    </div>

    <hr class="my-4 border-gray-200 dark:border-gray-700">

    {{-- Detail Konten --}}
    <div class="space-y-3 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Penyewa</span>
            <span
                class="font-medium text-gray-900 dark:text-white text-xs">{{ $record->customer->nama ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">No. Telepon</span>
            <span
                class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->customer->no_telp ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Lokasi Pengantaran</span>
            <span
                class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->lokasi_pengantaran ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Vendor</span>
            <span class="text-xs text-gray-900 dark:text-white font-semibold">{{ $record->car->garasi ?? 'N/A' }}</span>
        </div>

        <div class="flex justify-between">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Staff</span>
            <span class="font-medium bg-green-500 text-gray-900 dark:text-white text-xs">
                {{ $record->driver->nama ?? 'N/A' }}
            </span>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Jadwal Keluar</span>
            <div class="text-right">
                <p class="font-semibold text-xs">
                    Pukul {{ \Carbon\Carbon::parse($record->waktu_keluar)->locale('id')->format('H:i') }} WITA
                </p>
            </div>
        </div>

        <div class="flex justify-between items-center">
            <span class="text-gray-500 dark:text-gray-400 text-xs">Tanggal Keluar</span>
            <div class="text-right">
                <p class="font-semibold text-xs">
                    {{ \Carbon\Carbon::parse($record->tanggal_keluar)->locale('id')->isoFormat('dddd, D MMMM Y') }}
                </p>
            </div>
        </div>
    </div>

    {{-- Tombol Aksi --}}
    <div class="mt-6 flex items-center justify-end">
        {{-- PERBAIKAN DI SINI: Mengubah tombol menjadi aksi Livewire --}}
        @if ($canPerformActions)
            <div class="mt-6 flex items-center justify-end">
                <x-filament::button wire:click="pickupBooking({{ $record->id }})" wire:loading.attr="disabled"
                    icon="heroicon-o-arrow-top-right-on-square" style="background-color: {{ $buttonColor }};"
                    onmouseover="this.style.backgroundColor='{{ $buttonHover }}';"
                    onmouseout="this.style.backgroundColor='{{ $buttonColor }}';">
                    Pick Up
                </x-filament::button>
            </div>
        @endif
    </div>
</div>
