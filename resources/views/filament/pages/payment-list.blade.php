<x-filament-panels::page>
    <div class="space-y-4">
        <table class="min-w-full divide-y divide-gray-200 border">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left">ID Booking</th>
                    <th class="px-4 py-2 text-left">Nama Customer</th>
                    <th class="px-4 py-2 text-left">Nopol</th>
                    <th class="px-4 py-2 text-left">Tanggal Keluar</th>
                    <th class="px-4 py-2 text-left">Tanggal Kembali</th>

                    <th class="px-4 py-2 text-left">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach ($this->getPayments() as $booking)
                    <tr>
                        <td class="px-4 py-2">{{ $booking->id }}</td>
                        <td class="px-4 py-2">{{ $booking->customer->nama }}</td>
                        <td class="px-4 py-2">{{ $booking->car->nopol }}</td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($booking->tanggal_keluar)->locale('id')->isoFormat('d M Y') }}
                        </td>
                        <td class="px-4 py-2">
                            {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->locale('id')->isoFormat('d M Y') }}
                        </td>


                        <td class="px-4 py-2">
                            <a href="{{ \App\Filament\Pages\AgreementForm::getUrl(['booking' => $booking->booking_id]) }}"
                                class="px-3 py-1 bg-blue-600 text-white rounded hover:bg-blue-700" style="color: blue">
                                Tanda Tangan
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-filament-panels::page>
