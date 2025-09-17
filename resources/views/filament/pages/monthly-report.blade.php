<x-filament::page>
    <h2 class="text-xl font-bold mb-4">Rekapan Pendapatan Bulanan Tahun {{ $year }}</h2>

    <table class="w-full table-auto text-sm border">
        <thead class="bg-gray-800">
            <tr>
                <th class="border p-2">Bulan</th>
                <th class="border p-2">Sewa</th>
                <th class="border p-2">Denda</th>
                <th class="border p-2">Total</th>
                <th class="border p-2">Per Mobil</th>
            </tr>
        </thead>
        <tbody>
            @foreach(app('App\Filament\Pages\MonthlyReport')->getData() as $row)
                <tr>
                    <td class="border p-2">{{ $row['bulan'] }}</td>
                    <td class="border p-2">Rp {{ number_format($row['total_sewa'], 0, ',', '.') }}</td>
                    <td class="border p-2">Rp {{ number_format($row['total_denda'], 0, ',', '.') }}</td>
                    <td class="border p-2 font-bold">Rp {{ number_format($row['total'], 0, ',', '.') }}</td>
                    <td class="border p-2">
                        @foreach($row['per_mobil'] as $mobil => $total)
                            <div>{{ $mobil }}: Rp {{ number_format($total, 0, ',', '.') }}</div>
                        @endforeach
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-filament::page>
