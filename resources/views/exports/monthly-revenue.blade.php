<!DOCTYPE html>
<html>
<head>
    <title>Pendapatan Bulanan {{ $year }}</title>
    <style>
        body { font-family: sans-serif; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
    </style>
</head>
<body>
    <h2>Pendapatan Bulanan Tahun {{ $year }}</h2>
    <table>
    @foreach ($data as $month => $total)
        <tr>
            <td>{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</td>
            <td>Rp {{ number_format($total, 0, ',', '.') }}</td>
        </tr>
    @endforeach
</table>
</body>
</html>
