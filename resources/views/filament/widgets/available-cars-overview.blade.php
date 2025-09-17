<x-filament-widgets::widget>
    <x-filament::section>
        {{-- Slot untuk Judul Widget --}}
        <x-slot name="heading">
            Mobil Tersedia Saat Ini
        </x-slot>

        {{-- Grid untuk menampung kartu-kartu merek mobil --}}
        <div class="grid grid-cols-1 md:grid-cols-3 xl:grid-cols-3 gap-6">
            @forelse ($cars as $merek => $mobilList)
                {{-- Kartu untuk setiap merek mobil --}}
                <div class="bg-white rounded-xl shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-800 dark:ring-white/10">
                    {{-- Header Kartu Merek --}}
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">
                            {{ Illuminate\Support\Str::title($merek) }}
                            <span class="text-sm font-normal text-gray-500 dark:text-gray-400">
                                ({{ $mobilList->count() }} unit)
                            </span>
                        </h3>
                    </div>

                    {{-- Daftar mobil di dalam kartu --}}
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach ($mobilList as $mobil)
                            <li class="px-4 py-3  transition">
                                <div class="flex items-center justify-between">
                                    {{-- Nama Model Mobil --}}
                                    <p class="text-sm font-medium text-black dark:text-white truncate">
                                        {{ $mobil->carModel->name }}
                                    </p>
                                    {{-- Badge Nomor Polisi --}}
                                    <span class="text-xs font-semibold text-black bg-slate-950  dark:text-white px-2 py-1 rounded-md">
                                        {{ $mobil->nopol }}
                                    </span>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @empty
                {{-- Tampilan jika tidak ada mobil yang tersedia --}}
                <div class="col-span-full text-center py-8 text-gray-500 dark:text-gray-400">
                    Tidak ada mobil yang berstatus "Ready" saat ini.
                </div>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
