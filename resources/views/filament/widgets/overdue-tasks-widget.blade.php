<x-filament-widgets::widget>
    <x-filament::section>
        <x-slot name="heading">
            <span class="text-danger-500">🚨 Tugas Terlambat</span>
        </x-slot>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            {{-- Kolom Terlambat Pick Up --}}
            <div>
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-2">Terlambat Pick Up</h3>
                <div class="space-y-4">
                    @forelse ($overduePickups as $booking)
                        <div class="block p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $booking->car->carModel->name }} ({{ $booking->car->nopol }})</p>
                                    <p class="text-sm text-gray-600">{{ $booking->customer->nama }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-danger-600">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tombol aksi --}}
                            @if($canPerformActions)
                                <div class="mt-3 text-right">
                                    <x-filament::button
                                        wire:click="pickupOverdue({{ $booking->id }})"
                                        color="danger" size="xs">
                                        Pick Up
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">Tidak ada jadwal pick up yang terlewat.</div>
                    @endforelse
                </div>
            </div>

            {{-- Kolom Terlambat Selesaikan --}}
            <div>
                <h3 class="text-md font-semibold text-gray-700 dark:text-gray-200 mb-2">Terlambat Selesaikan</h3>
                <div class="space-y-4">
                    @forelse ($overdueReturns as $booking)
                        <div class="block p-4 bg-gray-50 dark:bg-gray-800 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="font-semibold">{{ $booking->car->carModel->name }} ({{ $booking->car->nopol }})</p>
                                    <p class="text-sm text-gray-600">{{ $booking->customer->nama }}</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-danger-600">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->diffForHumans() }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->format('d M Y') }}
                                    </p>
                                </div>
                            </div>

                            {{-- Tombol aksi --}}
                            @if($canPerformActions)
                                <div class="mt-3 text-right">
                                    <x-filament::button
                                        wire:click="returnOverdue({{ $booking->id }})"
                                        color="danger" size="xs">
                                        Selesaikan
                                    </x-filament::button>
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center text-gray-500 py-4">Tidak ada jadwal pengembalian yang terlewat.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
