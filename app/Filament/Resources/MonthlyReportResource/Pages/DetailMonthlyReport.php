<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use App\Filament\Exports\MonthlyDetailExporter;
use App\Filament\Exports\PaymentExporter;
use App\Filament\Resources\MonthlyReportResource;
use App\Models\Payment;

use Filament\Actions\Action; // <-- Import Action
use Filament\Resources\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TextFilter;
use Illuminate\Database\Eloquent\Builder;

class DetailMonthlyReport extends Page implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = MonthlyReportResource::class;

    protected static string $view = 'filament.resources.monthly-report-resource.pages.detail-monthly-report';

    public string $record;

    public function mount(string $record): void
    {
        $this->record = $record;
    }

    public function getTitle(): string
    {
        [$year, $month] = explode('-', $this->record);

        $monthName = \Carbon\Carbon::create()->month((int) $month)->locale('id')->isoFormat('MMMM');

        return "Detail Rekapan - {$monthName} {$year}";
    }

    // -- PERUBAHAN DI SINI: Mengubah headerActions menjadi method --
    protected function getHeaderActions(): array
    {
        [$year, $month] = explode('-', $this->record);

        return [
            // Action::make()
            //     ->label('Export Excel')
            //     ->exporter(PaymentExporter::class),

            // -- TOMBOL BARU UNTUK PDF --
            Action::make('exportPdf')
                ->label('Export Rekapan PDF')
                ->color('gray')
                ->icon('heroicon-o-document-arrow-down')
                // -- PERBAIKAN DI SINI: URL sekarang menyertakan filter aktif --
                ->url(function (): string {
                    [$year, $month] = explode('-', $this->record);

                    // Siapkan parameter dasar
                    $routeParams = ['year' => $year, 'month' => $month];

                    // Ambil nilai filter pelanggan yang aktif
                    $customerFilter = $this->tableFilters['customer']['value'] ?? null;
                    if ($customerFilter) {
                        $routeParams['customer_id'] = $customerFilter;
                    }

                    return route('reports.monthly-recap.pdf', $routeParams);
                })
                ->openUrlInNewTab(),
        ];
    }


    public function table(Table $table): Table
    {
        [$year, $month] = explode('-', $this->record);

        return $table
            ->query(
                Payment::query()
                    ->join('invoices', 'payments.invoice_id', '=', 'invoices.id')
                    ->join('bookings', 'invoices.booking_id', '=', 'bookings.id')
                    ->whereYear('bookings.tanggal_keluar', $year)
                    ->whereMonth('bookings.tanggal_keluar', $month)
            )
            ->columns([
                // TextColumn::make('invoice.id')->label('Faktur'),
                TextColumn::make('invoice.booking.customer.nama')->label('Penyewa')->searchable()
                    ->weight(FontWeight::Bold)
                    ->wrap()
                    ->width(150),
                TextColumn::make('invoice.booking.car.nopol')->label('No. Polisi')->searchable(),
                TextColumn::make('invoice.booking.tanggal_keluar')->label('Tanggal Keluar')->date('d M Y')->sortable(),
                TextColumn::make('invoice.booking.tanggal_kembali')->label('Tanggal Kembali')->date('d M Y')->sortable(),
                TextColumn::make('pembayaran')->label('Jumlah')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                TextColumn::make('status')->label('Status')->badge()->alignCenter()->colors([
                    'success' => 'lunas',
                    'danger' => 'belum_lunas',
                ])->formatStateUsing(fn($state) => match ($state) { 'lunas' => 'Lunas', 'belum_lunas' => 'Belum Lunas', default => ucfirst($state),
                    }),
            ])
            ->filters([
                SelectFilter::make('customer')
                    ->label('Filter Pelanggan')
                    ->relationship('invoice.booking.customer', 'nama')
                    ->searchable()
                    ->preload(),
            ]);
    }
}

