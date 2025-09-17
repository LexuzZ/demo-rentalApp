<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Perjanjian Sewa Kendaraan</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        /* HEADER */
        .header-section {
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            float: left;
        }

        .company-details {
            float: right;
            text-align: right;
        }

        .company-details h1 {
            margin: 0;
            font-size: 20px;
            color: #000;
        }

        .company-details p {
            margin: 0;
            font-size: 11px;
        }

        .clear {
            clear: both;
        }

        /* RULES */
        .rules {
            font-size: 11px;
            margin: 20px 0;
            line-height: 1.5;
        }

        .rules ol {
            padding-left: 15px;
        }

        /* DATA TABLE */
        .data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        .data-table td {
            padding: 5px;

        }

        /* CHECKBOX */
        .checkboxes {
            margin: 15px 0;
            font-size: 10px;
        }

        /* SIGNATURE */
        .signature {
            margin-top: 50px;
        }

        .signature div {
            display: inline-block;
            width: 45%;
            text-align: center;
        }

        .signature img {
            max-height: 100px;
            /* border: 1px solid #000; */
        }
    </style>
</head>

<body>
    <div class="container">
        {{-- HEADER --}}
        <div class="header-section">
            @php
                $imagePath = public_path('spt.png');
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $src = 'data:' . mime_content_type($imagePath) . ';base64,' . $imageData;
                } else {
                    $src = '';
                }
            @endphp

            @if ($src)
                <img src="{{ $src }}" alt="Logo" class="logo">
            @endif

            <div class="company-details">
                <h1>PERJANJIAN SEWA KENDARAAN</h1>
                <p>PT. Semeton Pesiar Trans</p>
                <p>Jl. Panji Tilaar Negara No.202, Kota Mataram, NTB</p>
                <p>Telp: 0819-0736-7197 / 0877-6559-9662</p>
            </div>
            <div class="clear"></div>
        </div>

        {{-- ATURAN --}}
        <div class="rules">
            <ol>
                <li>Pembatalan sewa kurang dari 1x24 jam dikenakan biaya penuh rental selama 1 hari sesuai type
                    kendaraan.</li>
                <li>Durasi rental adalah 12 jam atau 24 jam.</li>
                <li>Pihak penyewa bersedia dan tidak keberatan bilamana pihak PT. SEMETON PESIAR TRANS melakukan Survey,
                    verifikasi data dan dokumen kepada instansi terkait, bank, tetangga rumah, ataupun ditempat kerja.
                </li>
                <li>Calon penyewa kendaraan bersedia menunjukkan KTP asli dan dokumen asli lainnya kepada PT. SEMETON
                    PESIAR
                    TRANS
                    sebagai verifikasi keaslian data penyewa dan memberikan deposit (uang jaminan) minimal Rp. 2.500.000
                    (mobil)
                    dan Rp.
                    1.000.000 (sepeda motor) kepada pihak rental dan pengambilan deposit hanya melalui transfer bank
                    yaitu
                    maksimal (1x24
                    jam) setelah kendaraan diterima Kembali oleh pihak rental.</li>
                <li>Mobil tidak dilengkapi dengan Asuransi Allrisk (total kehilangan dan lecet). Apabila terjadi
                    kecelakaan
                    pihak penyewa di wajibkan membayar biaya perbaikan bengkel dan biaya sewa kendaraan selama di
                    bengkel sebesar 75% dari
                    harga sewa yang di sepakati</li>
                <li>Pembayaran sewa kendaraan penuh wajib maksimal pada saat penyerahan kendaraan.</li>
                <li>Kelebihan jam sewa (overtime) pemakaian akan dikenakan denda 20%/jam dari harga sewa kendaraan dan
                    maksimal
                    overtime 3 jam kelebihan pemakaian kendaraan di atas 3 jam akan di hitung sewa fullday/sehari.</li>
                <li>Khusus pengembalian/pengiriman di kantor PT. SEMETON PESIAR TRANS tidak dikenakan biaya, pengiriman
                    mobil ke
                    bandara akan dikenakan biaya 100.000 bila staff kami menggunakan kendaraan pribadi dan 75.000 untuk
                    biaya
                    dikenakan
                    jika team kami diantar ke PT SEMETON PESIAR TRANS.</li>
                <li>Pihak penyewa DILARANG memindah tangankan, apabila terbukti penyewa meminjamkan, menyewakan,
                    menggadaikan,
                    menjual kendaraan pada pihak manapun, maka perbuatan akan dikenakan denda 100.000.000.</li>
                <li>Pihak penyewa bersedia dan tidak keberatan seluruh pembayaran ke pihak rental (termasuk deposit)
                    tidak dapat
                    REFUND
                    apabila pihak penyewa terbukti melanggar point No. 9 dalam perjanjian ini.</li>
                <li>Pihak rental berhak mengambil tindakan langsung (mengamankan atau bahkan mengambil kendaraan secara
                    sepihak,
                    secara paksa, baik dengan merusak pagar, mendobrak pintu menggunakan alat-alat yang diperlukan).
                    Pihak
                    penyewa tidak
                    berhak melakukan tuntutan apapun, dan akan dikenakan denda apabila kendaraan kembali, dan pihak
                    penyewa
                    membebaskan pihak rental dari segala tuntutan hukum terkait tindakan yang diambil pihak rental.</li>
                <li>Selalu periksa kembali barang bawaan anda, pihak rental tidak bertanggung jawab atas segala
                    kehilangan
                    barang atau
                    tertinggal dalam kendaraan.</li>
                <li>Kendaraan hanya bisa digunakan di Lombok, pihak PT. SEMETON PESIAR TRANS melarang penyewa untuk
                    membawa
                    kendaraan keluar pulau Lombok, Overland dikenakan charge 200.000/hari.</li>
                <li>Penyewa wajib mengembalikan BBM seperti semula. Apabila saat kembali BBM kurang, maka akan dihitung
                    per bar
                    50.000
                    per range 10km/10.000.</li>
                <li>Kendaraan yang diserahkan dalam keadaan bersih kepada customer (penyewa) wajib dikembalikan dalam
                    keadaan
                    bersih
                    juga, apabila kendaraan dikembalikan dalam keadaan kotor, maka penyewa dikenakan biaya pencucian
                    sebesar
                    25.000.</li>
                <li>Penyewa wajib foto bersama kendaraan pada saat serah terima kendaraan, seluruh foto dan dokumentasi
                    adalah
                    sepenuhnya hak rental dan dapat digunakan untuk kepentingan pihak rental jika ada masalah hukum.
                </li>
                <li>Bila terjadi kecelakaan, kendaraan mogok/mengalami kendala lainnya dengan kendaraan yang disewa,
                    pihak
                    rental hanya
                    bertanggung jawab terhadap pergantian sparepart kendaraan/kelengkapannya lainnya tanpa seizin pihak
                    PT.
                    SEMETON
                    PESIAR TRANS.</li>
            </ol>
        </div>

        {{-- DATA PENYEWA --}}
        <table class="data-table">
            <tr>
                <td><strong>Nama Penyewa</strong></td>
                <td>{{ $booking->customer?->nama }}</td>
            </tr>
            <tr>
                <td><strong>No. KTP</strong></td>
                <td>{{ $booking->customer?->nik ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>No. Telepon</strong></td>
                <td>{{ $booking->customer?->telp ?? '-' }}</td>
            </tr>
            <tr>
                <td><strong>Detail Mobil</strong></td>
                <td>{{ $booking->car?->carModel?->name }} - {{ $booking->car?->nopol }}</td>
            </tr>
            <tr>
                <td><strong>Tgl/Waktu Sewa</strong></td>
                <td>{{ \Carbon\Carbon::parse($booking->tanggal_keluar)->format('d M Y') }}{{ $booking->waktu_keluar ? ' - ' . \Carbon\Carbon::parse($booking->waktu_keluar)->format('H:i') : '' }}
                    s/d
                    {{ \Carbon\Carbon::parse($booking->tanggal_kembali)->format('d M Y') }}
                    {{ $booking->waktu_kembali ? ' - ' . \Carbon\Carbon::parse($booking->waktu_kembali)->format('H:i') : '' }}({{ $booking->total_hari }}
                    hari)
                </td>
            </tr>
            <tr>
                <td><strong>Lokasi Pengantaran</strong></td>
                <td>{{ $booking->lokasi_pengantaran }} </td>
            </tr>
            <tr>
                <td><strong>Lokasi Pengembalian</strong></td>
                <td>{{ $booking->lokasi_pengembalian }} </td>
            </tr>
            <tr>
                <td><strong>Jaminan Sewa</strong></td>
                <td>☑ Motor &nbsp;&nbsp; ☑ STNK &nbsp;&nbsp;</td>
            </tr>
            <tr>
                @php
                    $method = ucfirst(strtolower($booking->invoice?->payment?->metode_pembayaran ?? '-'));
                @endphp
                <td><strong>Metode Pembayaran</strong></td>
                <td>{{ $method }}</td>

            </tr>
            <tr>
                <td><strong>Harga Harian</strong></td>
                <td>Rp {{ number_format($booking->car->harga_harian ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Harga Sewa</strong></td>
                <td>Rp {{ number_format($booking->estimasi_biaya ?? 0, 0, ',', '.') }}</td>
            </tr>
            @if (($booking->invoice->pickup_dropOff ?? 0) > 0)
                <tr>
                    <td><strong>Biaya Antar / Jemput</strong></td>
                    <td class="text-right">Rp {{ number_format($booking->invoice->pickup_dropOff, 0, ',', '.') }}</td>
                </tr>
            @endif
            @foreach ($booking->penalty as $penalty)
                <tr>
                    <td><strong>Klaim Garasi: {{ ucfirst($penalty->klaim) }}</strong></td>
                    <td class="text-right">Rp {{ number_format($penalty->amount, 0, ',', '.') }}</td>
                </tr>
            @endforeach
            <tr>
                <td><strong>Uang Muka (DP)</strong></td>
                <td class="text-right">Rp {{ number_format($booking->invoice->dp ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Total Tagihan</strong></td>
                <td>Rp {{ number_format($booking->invoice->total ?? 0, 0, ',', '.') }}</td>
            </tr>
            <tr>
                <td><strong>Sisa Pembayaran</strong></td>
                <td>Rp {{ number_format($booking->invoice->sisa_pembayaran ?? 0, 0, ',', '.') }}</td>
            </tr>
        </table>

        {{-- FOTO-FOTO --}}

        {{-- FOTO-FOTO --}}
        {{-- FOTO-FOTO --}}
        @if (!empty($foto_bbm) || !empty($foto_dongkrak) || !empty($foto_pelunasan) || !empty($foto_serah_terima))
            <h3 style="margin-top: 20px; margin-bottom: 10px;">Dokumentasi Foto</h3>

            <table style="width:100%; border-collapse: collapse;">
                <tr>
                    @if (!empty($foto_bbm))
                        <td style="width:25%; text-align:center; padding:5px;">
                            <h4 style="margin-bottom:5px;">Foto BBM</h4>
                            <img src="{{ $foto_bbm }}" alt="Foto BBM" style="max-width: 100%; height: auto;">
                        </td>
                    @endif

                    @if (!empty($foto_dongkrak))
                        <td style="width:25%; text-align:center; padding:5px;">
                            <h4 style="margin-bottom:5px;">Foto Dongkrak</h4>
                            <img src="{{ $foto_dongkrak }}" alt="Foto Dongkrak" style="max-width: 100%; height: auto;">
                        </td>
                    @endif

                    @if (!empty($foto_pelunasan))
                        <td style="width:25%; text-align:center; padding:5px;">
                            <h4 style="margin-bottom:5px;">Foto Pelunasan</h4>
                            <img src="{{ $foto_pelunasan }}" alt="Foto Pelunasan"
                                style="max-width: 100%; height: auto;">
                        </td>
                    @endif

                    @if (!empty($foto_serah_terima))
                        <td style="width:25%; text-align:center; padding:5px;">
                            <h4 style="margin-bottom:5px;">Foto Serah Terima</h4>
                            <img src="{{ $foto_serah_terima }}" alt="Foto Serah Terima"
                                style="max-width: 100%; height: auto;">
                        </td>
                    @endif
                </tr>
            </table>
        @endif

        <div style="margin-top:20px; page-break-inside: avoid;">

            {{-- METODE PEMBAYARAN --}}
            {{-- TANDA TANGAN --}}
            <div class="signature">
                <div>
                    <p>Hormat Kami,</p>

                    @php
                        $stampPath = public_path('stempel.png');
                        $stampData = file_exists($stampPath)
                            ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
                            : '';
                    @endphp

                    @if ($stampData)
                        <img src="{{ $stampData }}" alt="Stempel"
                            style="height: 80px; width: auto; opacity: 0.75; display: inline-block; vertical-align: middle; margin-bottom: 10px;">
                    @endif

                    <p class="signature-name">ACHMAD MUZAMMIL</p>
                    <p>Direktur</p>
                </div>

                <div>
                    <p>Penyewa</p>
                    @if ($booking->ttd)
                        <img src="{{ $booking->ttd }}" alt="TTD Penyewa" style="height:100px;">
                    @else
                        <p><em>TTD belum tersedia</em></p>
                    @endif
                    <p>({{ $booking->customer?->nama }})</p>
                </div>
            </div>

        </div>
</body>

</html>
