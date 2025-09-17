<x-filament-panels::page>
    <div x-data="{ scheduleData: @entangle('scheduleData') }">
        {{-- Filter Section --}}
        <x-filament::section>
            {{ $this->form }}
        </x-filament::section>

        {{-- Schedule Table Section --}}
        <x-filament::section class="mt-6">
            <div class="overflow-x-auto">
                <table class="w-full text-sm border-collapse">
                    <thead class="sticky top-0 z-10">
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            <th class="border p-2 font-semibold text-left sticky left-0 bg-gray-100 dark:bg-gray-800 z-20">Mobil</th>
                            <th class="border p-2 font-semibold text-left sticky left-[150px] bg-gray-100 dark:bg-gray-800 z-20">Nopol</th>
                            <th class="border p-2 font-semibold text-left sticky left-[250px] bg-gray-100 dark:bg-gray-800 z-20">Garasi</th>
                            <template x-for="day in scheduleData.daysInMonth">
                                <th class="border p-2 font-semibold text-center min-w-[50px]" x-text="day"></th>
                            </template>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="car in scheduleData.cars" :key="car.id">
                            <tr class="border-t">
                                <td class="border p-2 whitespace-nowrap sticky left-0 bg-white dark:bg-gray-900 z-10" x-text="car.model"></td>
                                <td class="border p-2 whitespace-nowrap sticky left-[150px] bg-white dark:bg-gray-900 z-10" x-text="car.nopol"></td>
                                <td class="border p-2 whitespace-nowrap sticky left-[250px] bg-white dark:bg-gray-900 z-10" x-text="car.garasi"></td>
                                <template x-for="day in scheduleData.daysInMonth">
                                    <td class="border p-0 text-center text-xs" {{-- Hapus padding dari td --}}
                                        :style="
                                            car.schedule[day] ? {
                                                'booking': 'background-color: #bee3f8;',
                                                'aktif':   'background-color: #c6f6d5;',
                                                'selesai': 'background-color: #e2e8f0;',
                                                'batal':   'background-color: #fed7d7;'
                                            }[car.schedule[day].status] || '' : ''
                                        ">
                                        {{-- PERUBAHAN DI SINI: Menambahkan link --}}
                                        <template x-if="car.schedule[day]">
                                            <a :href="`/admin/bookings/${car.schedule[day].booking_id}`"
                                               target="_blank"
                                               class="w-full h-full flex items-center justify-center p-1 hover:underline"
                                               :style="
                                                    car.schedule[day] ? {
                                                        'booking': 'color: #2c5282;',
                                                        'aktif':   'color: #22543d;',
                                                        'selesai': 'color: #4a5568;',
                                                        'batal':   'color: #9b2c2c;'
                                                    }[car.schedule[day].status] || '' : ''
                                               ">
                                                <span x-text="car.schedule[day].display_text"></span>
                                            </a>
                                        </template>
                                    </td>
                                </template>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
