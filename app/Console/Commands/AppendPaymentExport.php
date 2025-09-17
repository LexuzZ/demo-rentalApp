<?php

namespace App\Console\Commands;

use App\Filament\Exports\PaymentExporter;
use Illuminate\Console\Command;
use Maatwebsite\Excel\Facades\Excel;
use PHPExcel_IOFactory;

class AppendPaymentExport extends Command
{
     protected $signature = 'export:append-payments';
    protected $description = 'Export pembayaran dan append ke file Excel yang sama';

    public function handle()
    {
        $path = storage_path('app/public/payment-export.xlsx');

        if (file_exists($path)) {
            // Load file lama
            $spreadsheet = PHPExcel_IOFactory::load($path);
            $sheet = $spreadsheet->getActiveSheet();
            $rows = (new PaymentExporter())->collection();

            $startRow = $sheet->getHighestRow() + 1;

            foreach ($rows as $i => $row) {
                foreach ($row as $j => $value) {
                    $sheet->setCellValueByColumnAndRow($j + 1, $startRow + $i, $value);
                }
            }

            PHPExcel_IOFactory::createWriter($spreadsheet, 'Xlsx')->save($path);
            $this->info("Data berhasil ditambahkan ke $path");
        } else {
            // File belum ada, bikin baru
            Excel::store(new PaymentExporter(), 'payment-export.xlsx', 'public');
            $this->info("File baru dibuat di $path");
        }
    }
}
