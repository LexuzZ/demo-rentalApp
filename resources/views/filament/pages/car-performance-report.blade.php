<x-filament-panels::page>
    <div x-data="{
        isModalOpen: false,
        modalBookings: [],
        modalCarName: '',
        modalCarId: null,
        reportDateString: @entangle('reportDateString')
    }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Report Table Section --}}
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                <div class="flex justify-between items-center">
                    <span>Ringkasan Kinerja untuk Bulan {{ $reportTitle }}</span>
                    <x-filament::button wire:click="exportReport" icon="heroicon-o-arrow-down-tray" color="gray"
                        wire:loading.attr="disabled" wire:target="exportReport">
                        Export Excel
                    </x-filament::button>
                </div>
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                        <tr>
                            <th scope="col" class="px-4 py-3">Mobil</th>
                            <th scope="col" class="px-4 py-3">No. Polisi</th>
                            <th scope="col" class="px-4 py-3 text-center">Total Hari</th>
                            <th scope="col" class="px-4 py-3 text-right">Pendapatan</th>
                            <th scope="col" class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($reportTableData as $data)
                            <tr class="border-b dark:border-gray-700">
                                <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                    {{ $data['model'] }}
                                </td>
                                <td class="px-4 py-3">{{ $data['nopol'] }}</td>
                                <td class="px-4 py-3 text-center">{{ $data['days_rented'] }} hari</td>
                                <td class="px-4 py-3 text-right">Rp {{ number_format($data['revenue'], 0, ',', '.') }}
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <button
                                        @click="isModalOpen = true; modalBookings = @js($data['bookings']); modalCarName = '{{ $data['model'] }} ({{ $data['nopol'] }})'; modalCarId = {{ $data['car_id'] }}"
                                        class="text-primary-600 hover:text-primary-800 dark:text-primary-500 dark:hover:text-primary-400 font-medium">
                                        Lihat Detail
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-3 text-center">Tidak ada data kinerja mobil untuk
                                    periode ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section>

        {{-- STRUKTUR MODAL (POP-UP) --}}
        <div x-show="isModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50"
            style="display: none;">
            <div @click.away="isModalOpen = false"
                class="bg-white dark:bg-gray-800 rounded-lg shadow-xl w-full max-w-2xl mx-4">
                <div class="p-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white"
                        x-text="`Detail Booking untuk ${modalCarName}`"></h3>
                    <div class="mt-4 overflow-y-auto max-h-96">
                        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                            <thead
                                class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                                <tr>
                                    <th scope="col" class="px-4 py-3">Pelanggan</th>
                                    <th scope="col" class="px-4 py-3">Tanggal Sewa</th>
                                    <th scope="col" class="px-4 py-3 text-right">Pendapatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="booking in modalBookings" :key="booking.id">
                                    <tr class="border-b dark:border-gray-700">
                                        <td class="px-4 py-3" x-text="booking.customer"></td>
                                        <td class="px-4 py-3">
                                            <span
                                                x-text="new Date(booking.start).toLocaleDateString('id-ID', { day: 'numeric', month: 'short' })"></span>
                                            -
                                            <span
                                                x-text="new Date(booking.end).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' })"></span>
                                        </td>
                                        <td class="px-4 py-3 text-right"
                                            x-text="`Rp ${Number(booking.revenue).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 0 })}`">
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-6 flex justify-between items-center">
                        {{-- Tombol Export Excel --}}
                        <a :href="`/reports/export-car-bookings/${modalCarId}/${reportDateString.split('-')[0]}/${reportDateString.split('-')[1]}`"
                            class="fi-btn fi-btn-color-success" style="color: white; background-color: #22c55e; padding: 8px; border-radius: 12px;">
                            Export CSV
                        </a>
                        {{-- Tombol Tutup --}}
                        <button @click="isModalOpen = false" class="fi-btn fi-btn-color-gray">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
