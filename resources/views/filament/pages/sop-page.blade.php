<x-filament-panels::page>
    <div class="space-y-6">
        {{-- SOP PENCUCIAN MOBIL --}}
        <x-filament::section>
            <x-slot name="heading">
                Prosedur Kerja Pencucian Mobil
            </x-slot>

            <div class="space-y-4">
                <div>
                    <h3 class="font-semibold text-lg">A. Persiapan</h3>
                    <ul class="list-disc list-inside mt-2 space-y-1">
                        <li>Pastikan area pencucian bebas dari benda tajam dan aman.</li>
                        <li>Periksa kondisi mobil sebelum dicuci (cek goresan, penyok, kaca retak).</li>
                        <li>Pastikan semua kaca jendela dan pintu tertutup rapat.</li>
                        <li>Siapkan peralatan dan sabun.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">B. Pencucian Eksterior</h3>
                    <ul class="list-decimal list-inside mt-2 space-y-1">
                        <li>Bilas awal seluruh body mobil dari atas ke bawah.</li>
                        <li>Aplikasikan sabun menggunakan busa (foam) atau spons, mulai dari atap.</li>
                        <li>Sikat velg & ban menggunakan sikat khusus.</li>
                        <li>Bilas kembali dari atas ke bawah sampai sabun hilang.</li>
                        <li>Keringkan menggunakan lap microfiber.</li>
                    </ul>
                </div>
                <div>
                    <h3 class="font-semibold text-lg">C. Pembersihan Interior</h3>
                    <ul class="list-decimal list-inside mt-2 space-y-1">
                        <li>Buka semua pintu dan buang sampah dari kabin dan bagasi.</li>
                        <li>Sedot debu menggunakan vacuum cleaner (kursi, lantai, karpet, bagasi).</li>
                        <li>Bersihkan dashboard, panel pintu, dan area cup holder.</li>
                        <li>Bersihkan kaca bagian dalam dengan cairan pembersih kaca.</li>
                    </ul>
                </div>
            </div>
        </x-filament::section>

        {{-- SOP STAF GARASI --}}
        <x-filament::section>
            <x-slot name="heading">
                SOP Staf Garasi (Berbasis Aplikasi)
            </x-slot>
            <div class="prose dark:prose-invert max-w-none">
                <h4>Tanggung Jawab Utama:</h4>
                <ul>
                    <li>Memastikan kondisi mobil siap pakai.</li>
                    <li>Menginput, memperbarui, dan memantau data mobil di aplikasi.</li>
                    <li>Menjalankan proses serah-terima mobil sesuai prosedur.</li>
                    <li>Melaporkan kendala atau kerusakan pada atasan.</li>
                </ul>

                <h4>A. Penerimaan & Penyimpanan Mobil</h4>
                <ol>
                    <li>Login ke aplikasi website.</li>
                    <li>Cek jadwal kedatangan mobil dari fitur "Jadwal Kembali".</li>
                    <li>Saat mobil tiba: Catat waktu, foto kondisi mobil (upload), periksa bahan bakar & jarak tempuh.</li>
                    <li>Parkir mobil di posisi yang ditentukan.</li>
                </ol>

                <h4>B. Persiapan Mobil untuk Penyewaan</h4>
                <ol>
                    <li>Cek jadwal booking di aplikasi.</li>
                    <li>Pilih mobil yang akan disiapkan → klik "Status: Siap Pakai".</li>
                    <li>Lakukan pembersihan eksterior dan interior.</li>
                    <li>Periksa kelengkapan (STNK, ban cadangan, dll) dan bahan bakar.</li>
                    <li>Tandai di aplikasi bahwa mobil siap diambil.</li>
                </ol>

                <h4>C. Serah Terima Mobil ke Pelanggan</h4>
                <ol>
                    <li>Pastikan data penyewa sudah muncul di aplikasi dan cocokkan identitas.</li>
                    <li>Lakukan pemeriksaan bersama penyewa dan tandai checklist kondisi di aplikasi.</li>
                    <li>Foto mobil sebelum diserahkan (upload ke sistem).</li>
                    <li>Klik "Serah Terima" di aplikasi untuk mengubah status mobil menjadi "Disewa".</li>
                </ol>
            </div>
        </x-filament::section>

        {{-- SOP ADMIN GARASI --}}
        <x-filament::section>
            <x-slot name="heading">
                SOP Admin Garasi (Berbasis Aplikasi)
            </x-slot>
            <div class="prose dark:prose-invert max-w-none">
                <h4>1. Persiapan Awal</h4>
                <ul>
                    <li>Login ke aplikasi dan cek dashboard untuk melihat status mobil, jadwal, dan notifikasi.</li>
                </ul>

                <h4>2. Pengelolaan Data Mobil</h4>
                <ul>
                    <li>Input data mobil baru (nopol, merk, tipe, foto, tarif).</li>
                    <li>Update status mobil ("Tersedia", "Service", "Booking").</li>
                </ul>

                <h4>3. Pengelolaan Booking & Penyewaan</h4>
                <ul>
                    <li>Terima pesanan dari menu "Booking Masuk", verifikasi data penyewa dan pembayaran.</li>
                    <li>Koordinasikan dengan staf garasi untuk persiapan mobil.</li>
                    <li>Isi form serah terima digital, foto mobil, dan dapatkan tanda tangan.</li>
                </ul>

                <h4>4. Monitoring & Pengembalian</h4>
                <ul>
                    <li>Pantau masa sewa menggunakan fitur reminder.</li>
                    <li>Saat mobil kembali, cek kondisi, catat kilometer, dan isi form pengecekan di aplikasi.</li>
                    <li>Jika ada denda, input di aplikasi dan kirim invoice.</li>
                </ul>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>
