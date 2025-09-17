<div>
    <h3 class="text-lg font-medium leading-6 text-gray-900 dark:text-white mb-4">
        Riwayat Booking untuk {{ $car->carModel->name }} ({{ $car->nopol }})
    </h3>
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
            <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th scope="col" class="px-4 py-3">Pelanggan</th>
                    <th scope="col" class="px-4 py-3">Tgl. Keluar</th>
                    <th scope="col" class="px-4 py-3">Tgl. Kembali</th>
                    <th scope="col" class="px-4 py-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($bookings as $booking)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-3">{{ $booking->customer->nama }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}</td>
                        <td class="px-4 py-3">{{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}</td>
                        <td class="px-4 py-3">
                            <span @class([
                                'px-2 py-1 text-xs font-medium rounded-full',
                                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' => $booking->status === 'booking',
                                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' => $booking->status === 'disewa',
                                'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300' => $booking->status === 'selesai',
                                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' => $booking->status === 'batal',
                            ])>
                                {{ ucfirst($booking->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-center">Tidak ada data booking untuk mobil ini pada periode yang dipilih.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
