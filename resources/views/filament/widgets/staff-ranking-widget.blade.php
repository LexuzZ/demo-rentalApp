<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Slot untuk Judul Widget --}}
        <x-slot name="heading">
            Staff Paling Aktif - {{ $dateForHumans }}
        </x-slot>

        {{-- Date Picker untuk memilih tanggal --}}
        <div class="mb-4">
            {{-- PERBAIKAN DI SINI: Render form yang sudah didefinisikan di PHP --}}
            {{ $this->form }}
        </div>

        {{-- Tabel Peringkat Staff --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th scope="col" class="px-4 py-3">Rank</th>
                        <th scope="col" class="px-4 py-3">Staff</th>
                        <th scope="col" class="px-4 py-3 text-center">Total</th>
                        <th scope="col" class="px-4 py-3 text-center">Penyerahan</th>
                        <th scope="col" class="px-4 py-3 text-center">Pengembalian</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($stats as $stat)
                        <tr class="border-b dark:border-gray-700 @if($loop->first) bg-yellow-50 dark:bg-yellow-900/20 @endif">
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                @if($loop->first)
                                    <span>🏆</span>
                                @else
                                    {{ $loop->iteration }}
                                @endif
                            </td>
                            <td class="px-4 py-3 font-medium text-gray-900 whitespace-nowrap dark:text-white">
                                {{ $stat['staff_name'] }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold">{{ $stat['total'] }}</td>
                            <td class="px-4 py-3 text-center">{{ $stat['penyerahan'] }} 🚗</td>
                            <td class="px-4 py-3 text-center">{{ $stat['pengembalian'] }} ↩️</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-3 text-center">Tidak ada aktivitas staff pada tanggal ini.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <p class="text-xs text-gray-500 mt-2">
            ℹ️ Data menampilkan kinerja staff pada tanggal {{ $dateForHumans }}.
        </p>

    </x-filament::section>
</x-filament-widgets::widget>
