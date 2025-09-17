<?php

use App\Http\Controllers\CustomerFileController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\PdfController;
use App\Models\Booking;
use Carbon\Carbon;
use Filament\Http\Middleware\Authenticate;
use Filament\Notifications\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use function Pest\Laravel\get;

// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/{record}/pdf', [ExportController::class, 'download'])->name('invoices.pdf.download');
// Route::get('/api/cars', function () {
//     return \App\Models\Car::all()->map(function ($car) {
//         return [
//             'id' => $car->id,
//             'title' => $car->nama_mobil . ' (' . $car->nopol . ')',
//         ];
//     });
// });
// Route::get('/api/bookings-calendar', function () {
//     return \App\Models\Booking::with('car', 'customer')->get()->map(function ($booking) {
//         return [
//             'title' => $booking->customer->nama,
//             'start' => $booking->tanggal_keluar,
//             'end' => \Carbon\Carbon::parse($booking->tanggal_kembali),
//             'resourceId' => $booking->car_id,
//             'color' => '#3b82f6',
//         ];
//     });
// });
// routes/web.php


Route::get('/customers/{customer}/download-ktp', [CustomerFileController::class, 'downloadKtp'])->name('customers.download.ktp');
Route::get('/customers/{customer}/download-sim', [CustomerFileController::class, 'downloadSim'])->name('customers.download.sim');



Route::group(['middleware' => ['web', Authenticate::class]], function () {
    Route::get('/reports/export-car-bookings/{car}/{year}/{month}', [ExportController::class, 'exportCarBookings'])
        ->name('reports.export.car.bookings');
    // Routes untuk view file di browser


    // Route::get('/export/car-bookings/{month}/{year}', [ExportController::class, 'exportCarBookings']);

    // URL diubah menjadi /admin/bookings-calendar agar lebih konsisten
    Route::get('/invoices/{invoice}/pdf/download', [PdfController::class, 'downloadInvoice'])
        ->name('invoices.pdf.download');
    Route::get('/reports/monthly-recap/{year}/{month}/pdf', [PdfController::class, 'downloadMonthlyRecapPdf'])
        ->name('reports.monthly-recap.pdf');
    Route::get('/admin/bookings-calendar', function (Request $request) {
        $mobilModel = $request->query('mobil');
        $nopol = $request->query('nopol');

        $query = Booking::with(['car.carModel.brand', 'customer']);

        if ($mobilModel) {
            $query->whereHas('car.carModel', function ($q) use ($mobilModel) {
                $q->where('name', 'like', "%{$mobilModel}%");
            });
        }

        if ($nopol) {
            $query->whereHas('car', function ($q) use ($nopol) {
                $q->where('nopol', 'like', "%{$nopol}%");
            });
        }

        return $query->get()->map(function ($booking) {
            if (!$booking->car || !$booking->car->carModel || !$booking->car->carModel->brand || !$booking->customer) {
                return null;
            }

            $start = Carbon::createFromFormat('Y-m-d H:i:s', $booking->tanggal_keluar . ' ' . ($booking->waktu_keluar ?? '00:00:00'))->toDateTimeLocalString();
            $end = Carbon::createFromFormat('Y-m-d H:i:s', $booking->tanggal_kembali . ' ' . ($booking->waktu_kembali ?? '23:59:59'))->toDateTimeLocalString();

            $statusColor = match ($booking->status) {
                'booking' => '#3b82f6',
                'aktif' => '#10b981',
                'selesai' => '#6b7280',
                'batal' => '#ef4444',
                default => '#9ca3af',
            };

            $title = sprintf(
                '%s %s (%s) - %s',
                $booking->car->carModel->brand->name,
                $booking->car->carModel->name,
                $booking->car->nopol,
                $booking->customer->nama
            );

            return [
                'title' => $title,
                'start' => $start,
                'end' => $end,
                'color' => $statusColor,
                'id' => $booking->id,
            ];
        })->filter();
    });
});
