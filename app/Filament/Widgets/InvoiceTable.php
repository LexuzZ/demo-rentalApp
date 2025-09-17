<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Widgets\TableWidget as BaseWidget;

class InvoiceTable extends BaseWidget
{
    // protected static ?string $heading = 'Data Sewa Mobil Aktif';
    // protected static ?int $sort = 5;
    // protected int | string | array $columnSpan = 'full';



    // public function table(Table $table): Table
    // {

    //     return $table
    //         ->query(
    //             Booking::query()->where('status', 'aktif')->latest()
    //         )
    //         ->columns([
    //             TextColumn::make('status')
    //                 ->label('Status')
    //                 ->badge()
    //                 ->alignCenter()
    //                 ->colors([
    //                     'success' => 'aktif',
    //                     'info' => 'booking',
    //                     'gray' => 'selesai',
    //                     'danger' => 'batal',
    //                 ])
    //                 ->formatStateUsing(fn($state) => match ($state) {
    //                     'aktif' => 'Aktif',
    //                     'booking' => 'Booking',
    //                     'selesai' => 'Selesai',
    //                     'batal' => 'Batal',
    //                     default => ucfirst($state),
    //                 }),
    //             TextColumn::make('car.nopol')->label('No Polisi')->alignCenter(),
    //             TextColumn::make('car.nama_mobil')->label('Type Mobil')->alignCenter(),


    //             TextColumn::make('customer.nama')->label('Pelanggan')->alignCenter()->searchable(),
    //             // TextColumn::make('driver.nama')->label('Sopir')->toggleable(),

    //             TextColumn::make('tanggal_kembali')->label('Tanggal Kembali')->date('d M Y')->alignCenter(),

    //             TextColumn::make('waktu_kembali')->label('Waktu Kembali')->alignCenter()->time('H:i'),
    //             // TextColumn::make('estimasi_biaya')->label('Biaya')->money('IDR')->alignCenter(),
    //         ])
    //         ->filters([
    //             SelectFilter::make('status')
    //                 ->label('Status Pembayaran')
    //                 ->options([
    //                     'lunas' => 'Lunas',
    //                     'belum_lunas' => 'Belum Lunas',

    //                 ]),
    //         ])
    //         ->actions([
    //             Action::make('Selesai')
    //                 ->label('Selesaikan')
    //                 ->icon('heroicon-o-check-circle')
    //                 ->color('success')
    //                 ->url(fn(Booking $record) => route('filament.admin.resources.penalties.create', ['booking' => $record->id]))
    //                 ->openUrlInNewTab(), // atau hilangkan jika ingin buka di tab yang sama
    //         ]);
    // }
    protected static ?string $heading = 'Mobil Keluar Hari Ini';
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';
    public function table(Table $table): Table
    {
        return $table
            ->query(
    Booking::with('car')
        ->where('status', 'booking') // ✅ hanya status aktif
        ->whereDate('tanggal_keluar', \Carbon\Carbon::today()) // ✅ tanggal kembali hari ini
)
            ->columns([
                TextColumn::make('car.nopol')->label('No Polisi')->alignCenter(),
                TextColumn::make('car.merek')
                    ->label('Merk Mobil')
                    ->badge()
                    ->alignCenter()

                    ->formatStateUsing(fn($state) => match ($state) {
                        'toyota' => 'Toyota',
                        'mitsubishi' => 'Mitsubishi',
                        'suzuki' => 'Suzuki',
                        'honda' => 'Honda',
                        'daihatsu' => 'Daihatsu',
                        default => ucfirst($state),
                    }),
                    TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'aktif',
                        'info' => 'booking',
                        'gray' => 'selesai',
                        'danger' => 'batal',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'aktif' => 'Aktif',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state),
                    }),
                TextColumn::make('car.nama_mobil')->label('Nama Mobil')->alignCenter(),
                TextColumn::make('customer.nama')->label('Nama Penyewa')->alignCenter(),
                TextColumn::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->date('d M Y')->alignCenter(),
                TextColumn::make('waktu_keluar')->label('Waktu Keluar')->alignCenter()->time('H:i'),
            ])
            ->actions([
                Action::make('Selesai')
                    ->label('Pick Up')
                    ->icon('heroicon-o-check-circle')
                    ->color('info')
                    ->url(fn (Booking $record) => route('filament.admin.resources.bookings.edit', ['record' => $record->id]))

                    ->openUrlInNewTab(), // atau hilangkan jika ingin buka di tab yang sama
            ]);
    }
}
