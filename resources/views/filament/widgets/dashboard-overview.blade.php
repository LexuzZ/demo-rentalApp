@php
    $monthly = $this->monthly;
    $allTime = $this->allTime;
    $totalKlaim =
        ($monthly['klaim_bbm'] ?? 0) +
        ($monthly['klaim_overtime'] ?? 0) +
        ($monthly['klaim_baret'] ?? 0) +
        ($monthly['klaim_overland'] ?? 0) +
        ($monthly['klaim_washer'] ?? 0);
@endphp

<x-filament-widgets::widget>
    <x-filament::card>
        {{-- Tabel Ringkasan --}}
        <div class="space-y-6">
            {{-- BAGIAN BULAN INI --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Ringkasan Bulan Ini
                    ({{ \Carbon\Carbon::createFromDate($this->filters['tahun'] ?? now()->year, $this->filters['bulan'] ?? now()->month, 1)->isoFormat('MMMM YYYY') }})
                </h2>
                <table class="w-full mt-2 text-sm text-left text-gray-500 dark:text-gray-400">
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Profit Penjualan</td>
                            <td class="px-4 py-2 font-medium text-right text-success-600">Rp
                                {{ number_format($monthly['totalRevenue'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Pendapatan Sewa (Bruto)</td>
                            <td class="px-4 py-2 font-medium text-right text-success-600">Rp
                                {{ number_format($monthly['revenue_bruto'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Ongkir</td>
                            <td class="px-4 py-2 font-medium text-right text-success-600">Rp
                                {{ number_format($monthly['ongkir'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Total Klaim (BBM, Baret, dll)</td>
                            <td class="px-4 py-2 font-medium text-right text-success-600">Rp
                                {{ number_format($totalKlaim, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Kas Keluar</td>
                            <td class="px-4 py-2 font-medium text-right text-danger-600">Rp
                                {{ number_format($monthly['expense'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Piutang</td>
                            <td class="px-4 py-2 font-medium text-right text-warning-600">Rp
                                {{ number_format($monthly['piutang'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-bold bg-gray-50 dark:bg-gray-800">
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">Laba Bersih Bulan Ini</td>
                            <td
                                class="px-4 py-2 text-right {{ ($monthly['net_profit'] ?? 0) >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                Rp {{ number_format($monthly['net_profit'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- BAGIAN KESELURUHAN --}}
            <div>
                <h2 class="text-lg font-semibold text-gray-800 dark:text-gray-200">
                    Ringkasan Keseluruhan (All-Time)
                </h2>
                <table class="w-full mt-2 text-sm text-left text-gray-500 dark:text-gray-400">
                    <tbody class="divide-y dark:divide-gray-700">
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Total Profit Marketing</td>
                            <td class="px-4 py-2 font-medium text-right text-success-600">Rp
                                {{ number_format($allTime['profit_marketing'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Total Kas Keluar</td>
                            <td class="px-4 py-2 font-medium text-right text-danger-600">Rp
                                {{ number_format($allTime['expense'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50">
                            <td class="px-4 py-2">Total Piutang Berjalan</td>
                            <td class="px-4 py-2 font-medium text-right text-warning-600">Rp
                                {{ number_format($allTime['piutang'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                        <tr class="font-bold bg-gray-50 dark:bg-gray-800">
                            <td class="px-4 py-2 text-gray-800 dark:text-gray-200">Total Laba Bersih</td>
                            <td
                                class="px-4 py-2 text-right {{ ($allTime['net_profit'] ?? 0) >= 0 ? 'text-success-600' : 'text-danger-600' }}">
                                Rp {{ number_format($allTime['net_profit'] ?? 0, 0, ',', '.') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </x-filament::card>
</x-filament-widgets::widget>
