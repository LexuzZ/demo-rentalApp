<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekapan Faktur - {{ $startDate->isoFormat('MMMM YYYY') }}</title>
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
    </style>
</head>

<body>
    <div class="container">
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
                <img src="{{ $src }}" alt="Logo" class="logo" />
            @endif
            <div class="company-details">
                <h1>REKAPAN FAKTUR</h1>
                <p><strong>Semeton Pesiar Lombok</strong></p>
                <p>Jl. Batu Ringgit No.218, Kota Mataram</p>
            </div>
            <div class="clear"></div>
        </div>

        <div class="summary-section">
            <h2>RINGKASAN</h2>
            <p><strong>Total Transaksi:</strong> {{ $summary['total_transactions'] }}</p>
            <p><strong>Periode:</strong> {{ $startDate->isoFormat('D MMMM YYYY') }} -
                {{ $endDate->isoFormat('D MMMM YYYY') }}</p>
            <p><strong>Rincian Status:</strong></p>
            <ul>
                <li>Lunas: {{ $summary['status_breakdown']['lunas'] ?? 0 }} transaksi</li>
                <li>Belum Lunas: {{ $summary['status_breakdown']['belum_lunas'] ?? 0 }} transaksi</li>
            </ul>
        </div>

        <div class="details-section">
            <h2>DETAIL TRANSAKSI</h2>
            <table class="details-table">
                <thead>
                    <tr>
                        <th style="width: 35%;">DETAIL TRANSAKSI</th>
                        <th style="width: 10%;" class="text-center">DURASI</th>
                        <th style="width: 35%;">RINCIAN BIAYA</th>
                        <th style="width: 10%;" class="text-right">TOTAL</th>
                        <th style="width: 10%;" class="text-center">STATUS</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        @php
                            $booking = $payment->invoice->booking;
                            $totalDenda = $booking->penalty->sum('amount');
                            $totalTagihan = $booking->estimasi_biaya + $payment->invoice->pickup_dropOff + $totalDenda;
                        @endphp
                        <tr>
                            <td>
                                <strong>INV #{{ $payment->invoice->id }} / BOOK #{{ $booking->id }}</strong><br>
                                {{ $booking->customer->nama }}<br>
                                {{ $booking->car->carModel->name }} ({{ $booking->car->nopol }})<br>
                                <small>{{ $booking->tanggal_keluar }} s/d {{ $booking->tanggal_kembali }}</small>
                            </td>
                            <td class="text-center">{{ $booking->total_hari }} hari</td>
                            <td>
                                <ul>
                                    <li>Sewa: Rp {{ number_format($booking->estimasi_biaya, 0, ',', '.') }}</li>
                                    @if ($payment->invoice->pickup_dropOff > 0)
                                        <li>Antar/Jemput: Rp
                                            {{ number_format($payment->invoice->pickup_dropOff, 0, ',', '.') }}</li>
                                    @endif
                                    @if ($booking->penalty->count() > 0)
                                        @foreach ($booking->penalty as $penalty)
                                            <li>{{ ucfirst($penalty->klaim) }}: Rp
                                                {{ number_format($penalty->amount, 0, ',', '.') }}</li>
                                        @endforeach
                                    @endif
                                </ul>
                            </td>
                            <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                            <td class="text-center">
                                @php
                                    $statusText = '';
                                    $statusStyle = '';
                                    if ($payment->status == 'lunas') {
                                        $statusText = 'Lunas';
                                        // Hijau
                                    } else {
                                        $statusText = 'Belum Lunas'; // Merah
                                    }
                                @endphp
                                <span class="status-badge">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Tidak ada data pembayaran untuk periode ini.</td>
                        </tr>
                    @endforelse
                    @php
                        $grandTotal = $payments
                            ->map(function ($payment) {
                                $booking = $payment->invoice->booking;
                                $totalDenda = $booking->penalty->sum('amount');
                                return $booking->estimasi_biaya + $payment->invoice->pickup_dropOff + $totalDenda;
                            })
                            ->sum();
                    @endphp
                    <tr>
                        <td colspan="3" class="text-right"><strong>TOTAL SEMUA</strong></td>
                        <td colspan="2" class="text-right"><strong>Rp
                                {{ number_format($grandTotal, 0, ',', '.') }}</strong></td>
                        {{-- <td></td> --}}
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

        <div class="footer">
            <p>Dokumen ini dibuat oleh sistem Semeton Pesiar pada {{ now()->locale('id')->format('d F Y H:i') }}</p>
        </div>
    </div>
</body>

</html>
