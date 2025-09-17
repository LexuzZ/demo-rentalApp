<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice #{{ $invoice->id }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 12px;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .clear {
            clear: both;
        }

        /* =========================
   Header
   ========================= */
        .header-section {
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            height: auto;
            float: left;
        }

        .company-details {
            float: right;
            text-align: right;
        }

        .company-details h1 {
            margin: 0;
            font-size: 24px;
            color: #000;
        }

        .company-details p {
            margin: 0;
        }

        /* =========================
   Invoice & Billing Details
   ========================= */
        .invoice-details {
            margin-bottom: 20px;
        }

        .invoice-details table {
            width: 100%;
        }

        .text-right {
            text-align: right;
        }

        /* =========================
   Items Table
   ========================= */
        .items-table h3 {
            font-size: 14px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        .items-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .items-table th,
        .items-table td {
            border: 1px solid #eee;
            padding: 8px;
            text-align: left;
        }

        .items-table th {
            background-color: #f8f8f8;
        }

        /* =========================
   Totals
   ========================= */
        .totals-table {
            width: 55%;
            margin-top: 20px;
            float: right;
            border-collapse: collapse;
        }

        .totals-table td {
            padding: 5px 8px;
        }

        /* =========================
   Payment Details
   ========================= */
        .payment-details {
            margin-top: 40px;
        }

        .payment-details h3 {
            font-size: 14px;
            margin-bottom: 10px;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }

        /* =========================
   Signature Section
   ========================= */
        .signature-section {
            width: 170px;
            margin-top: 50px;
            text-align: center;
            float: right;
        }

        .signature-container {
            position: relative;
            height: 70px;
            /* ruang cukup untuk gambar */
        }

        .signature-image,
        .stamp-image {
            position: absolute;
            width: 90px;
            height: auto;
            left: 50%;
            margin-left: -120px;
        }

        .signature-image {
            top: 0;
            z-index: 10;
        }

        .signature-name {
            font-weight: bold;
            margin-top: 10px;
            padding-top: 5px;
            border-top: 1px solid #333;
        }

        /* =========================
   Footer
   ========================= */
        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 10px;
            color: #777;
            position: absolute;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header-section">
            {{-- PERUBAHAN 1: Menambahkan Logo --}}
            @php
                // Mengambil path gambar dan mengubahnya ke Base64 agar bisa di-embed di PDF
                $imagePath = public_path('spt.png');
                if (file_exists($imagePath)) {
                    $imageData = base64_encode(file_get_contents($imagePath));
                    $src = 'data:' . mime_content_type($imagePath) . ';base64,' . $imageData;
                } else {
                    $src = ''; // Kosongkan jika logo tidak ditemukan
                }
            @endphp
            @if ($src)
                <img src="{{ $src }}" alt="Logo" class="logo" />
            @endif

            <div class="company-details">
                <h1>FAKTUR SEWA</h1>
                <p>Semeton Pesiar Lombok</p>
                <p>Jl. Batu Ringgit No.218, Kota Mataram, NTB | Telp: 0819-0736-7197</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="invoice-details">
            <table>
                <tr>
                    <td>
                        <strong>Faktur No:</strong> #{{ $invoice->id }}<br>
                        <strong>Tanggal:</strong>
                        {{ \Carbon\Carbon::parse($invoice->tanggal_invoice)->format('d F Y') }}<br>
                        <strong>Booking ID:</strong> #{{ $invoice->booking->id }}
                    </td>
                    <td class="text-right">
                        <strong>Ditagihkan Kepada:</strong><br>
                        {{ $invoice->booking->customer->nama }}<br>
                        {{ $invoice->booking->customer->alamat }}<br>
                        {{ $invoice->booking->customer->no_telp }}
                    </td>
                </tr>
            </table>
        </div>

        <div class="items-table">
            <h3>Rincian Sewa</h3>
            <table>
                <thead>
                    <tr>
                        <th>Deskripsi</th>
                        <th class="text-right">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            Sewa Mobil: {{ $invoice->booking->car->carModel->brand->name }}
                            {{ $invoice->booking->car->carModel->name }} ({{ $invoice->booking->car->nopol }})
                            <br>
                            <small>
                                Dari: {{ \Carbon\Carbon::parse($invoice->booking->tanggal_keluar)->format('d M Y') }}
                                Sampai:
                                {{ \Carbon\Carbon::parse($invoice->booking->tanggal_kembali)->format('d M Y') }}
                                ({{ $invoice->booking->total_hari }} hari)
                            </small>
                            <br>
                            <small>
                                Harga per Hari: Rp
                                {{ number_format($invoice->booking->estimasi_biaya / $invoice->booking->total_hari, 0, ',', '.') }}

                            </small>
                        </td>
                        <td class="text-right">
                            Rp {{ number_format($invoice->booking->estimasi_biaya, 0, ',', '.') }}
                        </td>
                    </tr>
                    @if ($invoice->pickup_dropOff > 0)
                        <tr>
                            <td>Biaya Antar / Jemput</td>
                            <td class="text-right">Rp {{ number_format($invoice->pickup_dropOff, 0, ',', '.') }}</td>
                        </tr>
                    @endif
                    @foreach ($invoice->booking->penalty as $penalty)
                        <tr>
                            <td>Denda: {{ ucfirst($penalty->klaim) }}</td>
                            <td class="text-right">Rp {{ number_format($penalty->amount, 0, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="totals-table">
            <table class="totals-table">
                @php
                    $totalDenda = $invoice->booking->penalty->sum('amount');
                    $totalTagihan = $invoice->booking->estimasi_biaya + $invoice->pickup_dropOff + $totalDenda;
                    // $sisaPembayaran = $totalTagihan - $invoice->dp;
                    $sisaPembayaran = $totalTagihan - $invoice->dp;
                @endphp
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td>Uang Muka (DP)</td>
                    <td class="text-right">- Rp {{ number_format($invoice->dp, 0, ',', '.') }}</td>
                </tr>
                <tr>
                    <td><strong>Sisa Pembayaran</strong></td>
                    <td class="text-right"><strong>Rp
                            {{ number_format($sisaPembayaran, 0, ',', '.') }}</strong></td>
                </tr>
            </table>
        </div>

        <div class="clear"></div>

        {{-- PERUBAHAN 2: Menambahkan Informasi Pembayaran --}}
        <div class="payment-details">
            <h3>Metode Pembayaran</h3>
            <p>Silakan lakukan pembayaran ke salah satu rekening berikut:</p>
            <ul>
                <li><strong>Mandiri:</strong> 1610006892835 (a.n. ACHMAD MUZAMMIL)</li>
                <li><strong>BCA:</strong> 2320418758 (a.n. SRI NOVYANA)</li>
            </ul>
            <p>Mohon konfirmasi setelah melakukan pembayaran. Terima kasih.</p>
        </div>
        <div class="clear"></div>

        <div class="signature-section">
            @php
                // PERBAIKAN 1: Menggunakan nama file yang benar
                // $signaturePath = public_path('ttd.png');
                $stampPath = public_path('stempel.png');

                // $signatureData = file_exists($signaturePath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($signaturePath)) : '';
                $stampData = file_exists($stampPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
                    : '';
            @endphp

            <p>Hormat kami,</p>

            {{-- PERBAIKAN 2: Hanya menggunakan satu blok untuk menampilkan gambar --}}
            <div class="signature-container">
                {{-- @if ($stampData)
                    <img src="{{  $stampData }}" alt="Tanda Tangan" class="signature-image">
                @endif --}}
                @if ($stampData)
                    <img src="{{ $stampData }}" alt="Stempel"
                        style="height: 80px; width: auto; opacity: 0.75; display: inline-block; vertical-align: middle;">
                @endif
            </div>

            <p class="signature-name">ACHMAD MUZAMMIL</p>
            <p>CEO Company</p>
        </div>
        <div class="clear"></div>

        <div class="footer">
            <p>Terima kasih telah menggunakan jasa Semeton Pesiar.</p>
        </div>
    </div>
</body>

</html>
