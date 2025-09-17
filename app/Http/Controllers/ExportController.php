<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    public function exportCarBookings($carId, $year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate   = $startDate->copy()->endOfMonth()->endOfDay();

        // Ambil mobil beserta booking sesuai filter di CarPerformanceReport
        $car = Car::with(['carModel.brand', 'bookings' => function ($query) use ($startDate, $endDate) {
            $query->with('customer')
                ->where('status', '!=', 'batal')
                ->where(function ($q) use ($startDate, $endDate) {
                    $q->where('tanggal_keluar', '<=', $endDate)
                      ->where('tanggal_kembali', '>=', $startDate);
                });
        }])
        ->findOrFail($carId);

        // Siapkan file excel
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Judul
        $sheet->setCellValue('A1', 'Laporan Kinerja Mobil');
        $sheet->setCellValue('A2', $car->carModel->brand->name . ' ' . $car->carModel->name . ' - ' . $car->nopol);
        $sheet->setCellValue('A3', $startDate->isoFormat('MMMM YYYY'));
        $sheet->mergeCells('A1:E1');
        $sheet->mergeCells('A2:E2');
        $sheet->mergeCells('A3:E3');
        $sheet->getStyle('A1:A3')->getFont()->setBold(true);
        $sheet->getStyle('A1:A3')->getAlignment()->setHorizontal('center');


        // Header tabel
        $sheet->fromArray([
            ['Pelanggan', 'Tanggal Efektif Keluar', 'Tanggal Efektif Kembali', 'Hari Dihitung', 'Pendapatan (Prorata)']
        ], null, 'A5');
        $sheet->getStyle('A5:E5')->getFont()->setBold(true);


        $row = 6;
        $totalRevenue = 0;
        $totalDays = 0;

        foreach ($car->bookings as $booking) {
            $bookingStart = Carbon::parse($booking->tanggal_keluar)->startOfDay();
            $bookingEnd   = Carbon::parse($booking->tanggal_kembali)->startOfDay();

            $effectiveStartDate = $bookingStart->copy()->max($startDate);
            $effectiveEndDate   = $bookingEnd->copy()->min($endDate);

            // PERBAIKAN LOGIKA PERHITUNGAN HARI (non-inklusif)
            $days = $effectiveStartDate->diffInDays($effectiveEndDate);
            $daysInMonth = $days > 0 ? $days : 1; // Jika selisih 0 (sewa 1 hari), hitung sebagai 1

            $revenueInMonth = 0;
            if ($booking->total_hari > 0) {
                $dailyRate = $booking->estimasi_biaya / $booking->total_hari;
                $revenueInMonth = $dailyRate * $daysInMonth;
            }

            $totalRevenue += $revenueInMonth;
            $totalDays += $daysInMonth;

            // PERBAIKAN DATA YANG DITULIS: Menggunakan tanggal efektif
            $sheet->setCellValue("A{$row}", $booking->customer->nama);
            $sheet->setCellValue("B{$row}", $effectiveStartDate->format('d-m-Y'));
            $sheet->setCellValue("C{$row}", $effectiveEndDate->format('d-m-Y'));
            $sheet->setCellValue("D{$row}", $daysInMonth);
            $sheet->setCellValue("E{$row}", $revenueInMonth);
            $sheet->getStyle("E{$row}")->getNumberFormat()->setFormatCode('"Rp"#,##0');


            $row++;
        }

        // Ringkasan total
        $summaryRow = $row + 1;
        $sheet->setCellValue("A{$summaryRow}", 'TOTAL');
        $sheet->setCellValue("D{$summaryRow}", $totalDays);
        $sheet->setCellValue("E{$summaryRow}", $totalRevenue);
        $sheet->getStyle("A{$summaryRow}:E{$summaryRow}")->getFont()->setBold(true);
        $sheet->getStyle("E{$summaryRow}")->getNumberFormat()->setFormatCode('"Rp"#,##0');

        // Atur lebar kolom
        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Simpan & download
        $writer = new Xlsx($spreadsheet);

        $filename = 'laporan_kinerja_' . str_replace(' ', '_', $car->nopol) . '_' . $year . '_' . $month . '.xlsx';

        return new StreamedResponse(function () use ($writer) {
            $writer->save('php://output');
        }, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => "attachment;filename=\"$filename\"",
            'Cache-Control' => 'max-age=0',
        ]);
    }
}
