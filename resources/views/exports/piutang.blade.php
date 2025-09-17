<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Piutang - {{ now()->isoFormat('MMMM YYYY') }}</title>
    <style>
        body {
            font-family: 'Helvetica', sans-serif;
            font-size: 10px;
            color: #333;
        }

        .container {
            width: 100%;
            margin: 0 auto;
        }

        .header-section {
            margin-bottom: 20px;
        }

        .logo {
            width: 150px;
            height: auto;
            float: left;
        }

        .company-details {
            text-align: right;
            float: right;
        }

        .company-details h1 {
            margin: 0;
            font-size: 20px;
            color: #000;
        }

        .summary-section,
        .details-section {
            margin-bottom: 20px;
        }

        .summary-section h2,
        .details-section h2 {
            border-bottom: 1px solid #eee;
            padding-bottom: 5px;
            margin-bottom: 10px;
            font-size: 14px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            vertical-align: top;
        }

        .details-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 50px;
            font-size: 9px;
            color: #777;
            position: absolute;
            bottom: 20px;
            width: 100%;
        }

        .clear {
            clear: both;
        }

        ul {
            padding-left: 15px;
            margin: 0;
        }

        li {
            margin-bottom: 2px;
        }

        .signature-section {
            width: 170px;
            margin-top: 50px;
            text-align: center;
            float: right;
        }

        .signature-container {
            position: relative;
            height: 70px;
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
    </style>
</head>

<body>
    <div class="container">
        <div class="header-section">
            @php
                $imagePath = public_path('spt.png');
                $src = file_exists($imagePath)
                    ? 'data:' .
                        mime_content_type($imagePath) .
                        ';base64,' .
                        base64_encode(file_get_contents($imagePath))
                    : '';
            @endphp
            @if ($src)
                <img src="{{ $src }}" alt="Logo" class="logo" />
            @endif
            <div class="company-details">
                <h1>LAPORAN PIUTANG</h1>
                <p><strong>Semeton Pesiar Lombok</strong></p>
                <p>Jl. Batu Ringgit No.218, Kota Mataram</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="summary-section">
            <h2>RINGKASAN</h2>
            <p><strong>Total Piutang:</strong> {{ $piutang->count() }} transaksi</p>
            <p><strong>Periode Cetak:</strong> {{ now()->isoFormat('D MMMM YYYY') }}</p>
            <p><strong>Status:</strong> Semua transaksi dalam laporan ini adalah <em>Belum Lunas</em>.</p>
        </div>

        <div class="details-section">
            <h2>DETAIL PIUTANG</h2>
            <table class="details-table">
                <thead>
                    <tr>
                        <th style="width: 40%;">DETAIL TRANSAKSI</th>
                        <th style="width: 10%;" class="text-center">DURASI</th>
                        <th style="width: 30%;">RINCIAN BIAYA</th>
                        <th style="width: 20%;" class="text-center">PENYEWA</th>
                        <th style="width: 20%;" class="text-right">JUMLAH</th>
                        <th style="width: 20%;" class="text-right">Sisa Pembayaran</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($piutang as $item)
                        @php
                            // $booking = $payment->invoice->booking;
                            $totalDenda = $item->invoice->booking->penalty->sum('amount');
                            $totalTagihan =
                                $item->invoice->booking->estimasi_biaya + $item->invoice->pickup_dropOff + $totalDenda;
                            $sisaPembayaran = $item->invoice->dp - $totalTagihan;
                        @endphp
                        <tr>
                            <td>
                                <strong>INV #{{ $item->invoice->id }} / BOOK
                                    #{{ $item->invoice->booking->id }}</strong><br>
                                {{ $item->invoice->booking->car->carModel->name }}
                                ({{ $item->invoice->booking->car->nopol }})
                                <br>
                                <small>Harga Harian: Rp
                                    {{ number_format($item->invoice->booking->harga_harian, 0, ',', '.') }}</small><br>
                                <small>{{ $item->invoice->booking->tanggal_keluar }} s/d
                                    {{ $item->invoice->booking->tanggal_kembali }} </small>
                                <br>
                                <small>{{ $item->invoice->booking->waktu_keluar }} WITA s/d
                                    {{ $item->invoice->booking->waktu_kembali }} WITA
                                </small>
                            </td>
                            <td class="text-center">{{ $item->invoice->booking->total_hari }} hari</td>


                            <td>
                                <ul>
                                    <li>Sewa: Rp
                                        {{ number_format($item->invoice->booking->estimasi_biaya, 0, ',', '.') }}</li>
                                    @if ($item->invoice->pickup_dropOff > 0)
                                        <li>Antar/Jemput: Rp
                                            {{ number_format($item->invoice->pickup_dropOff, 0, ',', '.') }}</li>
                                    @endif
                                    @if ($item->invoice->booking->penalty->count() > 0)
                                        @foreach ($item->invoice->booking->penalty as $penalty)
                                            <li>{{ ucfirst($penalty->klaim) }}: Rp
                                                {{ number_format($penalty->amount, 0, ',', '.') }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                            <td class="text-center">{{ $item->invoice->booking->customer->nama }}</td>
                            <td class="text-right">Rp {{ number_format($item->pembayaran, 0, ',', '.') }}</td>
                            <td class="text-right">Rp {{ number_format($sisaPembayaran, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">Tidak ada data piutang.</td>
                        </tr>
                    @endforelse
                    @php
                        $grandTotal = $piutang->sum('pembayaran');
                        $totalTagihan = $piutang
                            ->filter(fn($item) => $item->status === 'belum_lunas')
                            ->map(function ($item) {
                                $booking = $item->invoice->booking;
                                $totalDenda = $booking->penalty->sum('amount');
                                $totalTagihan = $booking->estimasi_biaya + $item->invoice->pickup_dropOff + $totalDenda;
                                return $totalTagihan - $item->invoice->dp; // sisa pembayaran
                            })
                            ->sum();
                    @endphp
                    <tr>
                        <td colspan="4" class="text-right"><strong>TOTAL PIUTANG</strong></td>
                        <td colspan="2" class="text-right"><strong>Rp
                                {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                    </tr>
                    <tr>
                        <td colspan="4" class="text-right"><strong>TOTAL TAGIHAN SISA PEMBAYARAN</strong></td>
                        <td colspan="2" class="text-right"><strong>Rp
                                {{ number_format($totalTagihan, 0, ',', '.') }}</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
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
                $stampPath = public_path('stempel.png');
                $stampData = file_exists($stampPath)
                    ? 'data:image/png;base64,' . base64_encode(file_get_contents($stampPath))
                    : '';
            @endphp

            <p>Hormat kami,</p>
            <div class="signature-container">
                @if ($stampData)
                    <img src="{{ $stampData }}" alt="Stempel"
                        style="height: 80px; width: auto; opacity: 0.75; display: inline-block; vertical-align: middle;">
                @endif
            </div>
            <p class="signature-name">ACHMAD MUZAMMIL</p>
            <p>CEO Company</p>
        </div>

        <div class="footer">
            <p>Dokumen ini dibuat oleh sistem Semeton Pesiar pada {{ now()->locale('id')->format('d F Y H:i') }}</p>
        </div>
    </div>
</body>

</html>
