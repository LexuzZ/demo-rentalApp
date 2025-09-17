<?php

namespace App\Filament\Resources;

use Illuminate\Support\Str;
use App\Filament\Resources\BookingResource\Pages;
use App\Models\Booking;
use App\Models\Brand;
use App\Models\Car;
use App\Models\CarModel;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists; // <-- 1. Import Infolist
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BookingResource extends Resource
{
    protected static ?string $model = Booking::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pemesanan';
    protected static ?string $pluralLabel = 'Pemesanan Sewa';

    /**
     * Fungsi terpusat untuk menghitung total hari dan estimasi biaya.
     */

    protected static function calculatePrice(callable $set, callable $get)
    {
        $tanggalKeluar = $get('tanggal_keluar');
        $tanggalKembali = $get('tanggal_kembali');
        $hargaHarian = (int) $get('harga_harian');

        if (!$tanggalKeluar || !$tanggalKembali || !$hargaHarian) {
            $set('estimasi_biaya', 0);
            $set('total_hari', 0);
            return;
        }

        $start = Carbon::parse($tanggalKeluar);
        $end = Carbon::parse($tanggalKembali);
        $days = $start->diffInDays($end);

        $totalHari = $days > 0 ? $days : 1;

        $set('total_hari', $totalHari);
        $set('estimasi_biaya', $hargaHarian * $totalHari);
    }

    public static function form(Form $form): Form
    {
        // PERBAIKAN: Mengembalikan skema form yang lengkap
        $isNotAdmin = !Auth::user()->hasAnyRole(['superadmin', 'admin']);

        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\TextInput::make('id')->hidden()->dehydrated(),

                Forms\Components\DatePicker::make('tanggal_keluar')
                    ->label('Tanggal Keluar')
                    ->required()
                    ->live() // Penting untuk memicu refresh pada dropdown mobil
                    ->afterStateUpdated(fn(callable $set, callable $get) => static::calculatePrice($set, $get))
                    ->disabled($isNotAdmin),

                Forms\Components\DatePicker::make('tanggal_kembali')
                    ->label('Tanggal Kembali')
                    ->required()
                    ->live() // Penting untuk memicu refresh pada dropdown mobil
                    ->afterStateUpdated(fn(callable $set, callable $get) => static::calculatePrice($set, $get))
                    ->disabled($isNotAdmin),

                Forms\Components\TimePicker::make('waktu_keluar')->label('Waktu Keluar')->seconds(false)->disabled($isNotAdmin),
                Forms\Components\TimePicker::make('waktu_kembali')->label('Waktu Kembali')->seconds(false)->disabled($isNotAdmin),

                Forms\Components\Select::make('garasi_type')
                    ->label('Pilih Garasi')
                    ->options([
                        'spt' => 'Garasi DEMO',
                        'vendor' => 'Garasi Vendor',
                    ])
                    ->live()
                    ->afterStateUpdated(fn(Forms\Set $set) => $set('car_id', null)) // Kosongkan pilihan mobil
                    ->dehydrated(false), // Field ini virtual, tidak disimpan

                Forms\Components\Select::make('car_id')
                    ->label('Unit Mobil Tersedia')
                    ->relationship(
                        name: 'car',
                        titleAttribute: 'nopol',
                        modifyQueryUsing: function (Builder $query, Forms\Get $get, ?Model $record) {
                            // Saat EDIT, tampilkan mobil yg sudah dipilih tanpa filter ketat
                            if ($record && $record->exists) {
                                return $query->orWhere('id', $record->car_id);
                            }

                            $startDate = $get('tanggal_keluar');
                            $endDate = $get('tanggal_kembali');
                            $garasiType = $get('garasi_type');

                            if (!$startDate || !$endDate || !$garasiType) {
                                return $query->whereRaw('1 = 0');
                            }

                            // Filter berdasarkan garasi
                            if ($garasiType === 'spt') {
                                $query->where('garasi', 'DEMO');
                            } else {
                                $query->where('garasi', '!=', 'DEMO');
                            }

                            // Filter ketersediaan mobil
                            return $query
                                ->whereNotIn('status', ['perawatan', 'nonaktif'])
                                ->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($startDate, $endDate) {
                                $bookingQuery
                                    ->whereIn('status', ['booking', 'disewa']) // ✅ hanya hitung yang masih berlaku
                                    ->where(function (Builder $q) use ($startDate, $endDate) {
                                        $q->where('tanggal_keluar', '<', $endDate)
                                            ->where('tanggal_kembali', '>', $startDate);
                                    });
                            });
                        }
                    )
                    ->getOptionLabelFromRecordUsing(function (Car $record) {
                        $label = "{$record->carModel->name} ({$record->nopol})";
                        if ($record->garasi !== 'SPT') {
                            $label .= " - {$record->garasi}";
                        }
                        return $label;
                    })
                    ->preload()
                    ->live()
                    ->searchable()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set, callable $get) {
                        $car = Car::find($state);
                        $set('harga_harian', $car?->harga_harian ?? 0);
                        static::calculatePrice($set, $get);
                    })
                    ->disabled($isNotAdmin),



                Forms\Components\Select::make('customer_id')
                    ->label('Penyewa')
                    ->relationship('customer', 'nama')
                    ->searchable()->preload()
                    ->createOptionForm([
                        Forms\Components\TextInput::make('nama')->label('Nama Penyewa')->required(),
                        Forms\Components\TextInput::make('no_telp')->label('No. HP')->tel()->required()->unique(ignoreRecord: true),
                        Forms\Components\TextInput::make('alamat')->label('Alamat')->required(),
                        Forms\Components\TextInput::make('ktp')->label('No KTP')->required()->unique(ignoreRecord: true),
                    ])
                    ->createOptionAction(fn(Forms\Components\Actions\Action $action) => $action->disabled($isNotAdmin))
                    ->required()
                    ->disabled($isNotAdmin),

                Forms\Components\Select::make('driver_id')->label('Staff Bertugas')->relationship('driver', 'nama')->searchable()->preload()->nullable(),
                Forms\Components\Select::make('paket')->label('Paket Sewa')->options(['lepas_kunci' => 'Lepas Kunci', 'dengan_driver' => 'Dengan Driver', 'tour' => 'Paket Tour', 'kontrak' => 'Kontrak'])->nullable()->disabled($isNotAdmin),
                Forms\Components\Textarea::make('lokasi_pengantaran')->label('Lokasi Pengantaran')->nullable()->rows(2)->columnSpanFull()->disabled($isNotAdmin),
                Forms\Components\Textarea::make('lokasi_pengembalian')->label('Lokasi Pengembalian')->nullable()->rows(2)->columnSpanFull()->disabled($isNotAdmin),
                Forms\Components\TextInput::make('harga_harian')->label('Harga Harian')->prefix('Rp')->numeric()->dehydrated()->live()->afterStateUpdated(fn(callable $set, callable $get) => static::calculatePrice($set, $get))->disabled($isNotAdmin),
                Forms\Components\TextInput::make('total_hari')->label('Total Hari Sewa')->numeric()->disabled()->dehydrated(),
                Forms\Components\TextInput::make('estimasi_biaya')->label('Total Sewa')->prefix('Rp')->dehydrated(true)->required()->disabled($isNotAdmin),

                // PERUBAHAN 1: Menyamakan nilai status
                Forms\Components\Select::make('status')
                    ->label('Status Pemesanan')
                    ->options(['booking' => 'Booking', 'disewa' => 'Disewa', 'selesai' => 'Selesai', 'batal' => 'Batal'])
                    ->default('booking')
                    ->required(),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Kirim ke Penyewa')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('createInvoice')
                                ->label('Buat Faktur Sewa')
                                ->icon('heroicon-o-document-plus')
                                ->color('primary')
                                ->visible(fn(Booking $record) => !$record->invoice)
                                ->url(fn(Booking $record) => InvoiceResource::getUrl('create', ['booking_id' => $record->id])),
                            Infolists\Components\Actions\Action::make('addPayment')
                                ->label('Tambah Pembayaran')
                                ->icon('heroicon-o-banknotes')
                                ->color('success')
                                ->visible(fn(Booking $record) => $record->invoice && !$record->invoice->payment) // ✅ hanya muncul jika ada invoice, tapi belum ada payment
                                ->url(fn(Booking $record) => \App\Filament\Resources\PaymentResource::getUrl('create', [
                                    'invoice_id' => $record->invoice->id
                                ])),
                            Infolists\Components\Actions\Action::make('viewPayment')
                                ->label('Edit Pembayaran')
                                ->icon('heroicon-o-eye')
                                ->color('gray')
                                // Hanya muncul jika pembayaran SUDAH ada
                                ->visible(fn(Booking $record) => $record->invoice && $record->invoice->payment)
                                ->url(fn(Booking $record) => PaymentResource::getUrl('edit', ['record' => $record->invoice->payment->id])),
                            Infolists\Components\Actions\Action::make('addPenalty')
                                ->label('Tambah Klaim')
                                ->icon('heroicon-o-exclamation-triangle')
                                ->color('danger')
                                ->url(fn(Booking $record) => PenaltyResource::getUrl('create', ['booking_id' => $record->id])),
                            // Infolists\Components\Actions\Action::make('whatsapp')
                            //     ->label('Via WhatsApp')
                            //     ->icon('heroicon-o-chat-bubble-left-right')
                            //     ->color('success')
                            //     ->url(function (Booking $record) {
                            //         // 1. Ambil nomor telepon
                            //         $phone = $record->customer->no_telp;

                            //         // 2. Bersihkan nomor (hapus spasi, -, dll)
                            //         $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);

                            //         // 3. Ganti '0' di depan dengan '62' jika ada
                            //         if (substr($cleanedPhone, 0, 1) === '0') {
                            //             $cleanedPhone = '62' . substr($cleanedPhone, 1);
                            //         }

                            //         $carDetails = "{$record->car->carModel->brand->name} {$record->car->carModel->name} ({$record->car->nopol})";
                            //         $tglKeluar = Carbon::parse($record->tanggal_keluar)->isoFormat('dddd, D MMMM YYYY');
                            //         $waktuKeluar = $record->waktu_keluar ? ' pukul ' . Carbon::parse($record->waktu_keluar)->format('H:i') . ' WITA' : '';
                            //         $tglKembali = Carbon::parse($record->tanggal_kembali)->isoFormat('dddd, D MMMM YYYY');
                            //         $waktuKembali = $record->waktu_kembali ? ' pukul ' . Carbon::parse($record->waktu_kembali)->format('H:i') . ' WITA' : '';
                            //         $paket = match ($record->paket) {
                            //             'lepas_kunci' => 'Lepas Kunci',
                            //             'dengan_driver' => 'Dengan Driver',
                            //             'tour' => 'Paket Tour',
                            //             default => '-'
                            //         };
                            //         $totalBiaya = 'Rp ' . number_format($record->estimasi_biaya, 0, ',', '.');
                            //         $totalHari = ($record->total_hari) . ' Hari ';

                            //         $message = "Halo *{$record->customer->nama}*,\n\n";
                            //         $message .= "Ini adalah konfirmasi untuk detail pemesanan sewa mobil Anda di *Semeton Pesiar*:\n\n";
                            //         $message .= "🚗 *Mobil:* {$carDetails}\n";
                            //         $message .= "📦 *Paket:* {$paket}\n";
                            //         $message .= "➡️ *Waktu Keluar:* {$tglKeluar}{$waktuKeluar}\n";
                            //         $message .= "⬅️ *Waktu Kembali:* {$tglKembali}{$waktuKembali}\n";
                            //         $message .= "📍 *Lokasi Antar:* " . ($record->lokasi_pengantaran ?: '-') . "\n";
                            //         $message .= "📍 *Lokasi Kembali:* " . ($record->lokasi_pengembalian ?: '-') . "\n";
                            //         $message .= "💰 *Total Biaya Sewa:* {$totalBiaya}\n";
                            //         $message .= "💰 *Total Hari Sewa:* {$totalHari}\n\n";
                            //         $message .= "Mohon konfirmasinya. Terima kasih 🙏";


                            //         // 5. Buat URL WhatsApp
                            //         return 'https://wa.me/' . $cleanedPhone . '?text=' . urlencode($message);
                            //     })
                            //     ->openUrlInNewTab()


                        ]),
                    ]),

                Infolists\Components\Section::make('Informasi Booking')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('status')
                                ->badge()
                                ->colors(['success' => 'aktif', 'info' => 'booking', 'gray' => 'selesai', 'danger' => 'batal'])
                                ->formatStateUsing(fn($state) => match ($state) {
                                    'aktif' => 'Aktif',
                                    'booking' => 'Booking',
                                    'selesai' => 'Selesai',
                                    'batal' => 'Batal',
                                    default => ucfirst($state)
                                }),
                            Infolists\Components\TextEntry::make('paket')
                                ->badge()
                                ->formatStateUsing(fn($state) => match ($state) {
                                    'lepas_kunci' => 'Lepas Kunci',
                                    'dengan_driver' => 'Dengan Driver',
                                    'tour' => 'Paket Tour',
                                    default => '-'
                                }),
                            Infolists\Components\TextEntry::make('driver.nama')->label('Staff Bertugas'),
                        ]),
                    ]),

                Infolists\Components\Section::make('Detail Jadwal & Biaya')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('tanggal_keluar')->dateTime('d M Y'),
                            Infolists\Components\TextEntry::make('tanggal_kembali')->dateTime('d M Y'),
                            Infolists\Components\TextEntry::make('total_hari')->suffix(' Hari'),
                            Infolists\Components\TextEntry::make('waktu_keluar')->dateTime('H:i')->suffix(' WITA'),

                            Infolists\Components\TextEntry::make('waktu_kembali')->dateTime('H:i')->suffix(' WITA'),

                            Infolists\Components\TextEntry::make('estimasi_biaya')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                        ]),
                    ]),
                Infolists\Components\Section::make('Rincian Biaya')
                    ->schema([
                        Infolists\Components\Grid::make(2)->schema([
                            Infolists\Components\TextEntry::make('estimasi_biaya')
                                ->label('Biaya Sewa')
                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                            Infolists\Components\TextEntry::make('invoice.pickup_dropOff')
                                ->label('Biaya Antar/Jemput')
                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),

                            Infolists\Components\TextEntry::make('invoice.dp')
                                ->label('Uang Muka (DP)')
                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state ?? 0, 0, ',', '.')),

                            Infolists\Components\TextEntry::make('total_denda')
                                ->label('Total Denda')
                                ->state(fn(\App\Models\Booking $record) => $record->penalty?->sum('amount') ?? 0)
                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                            Infolists\Components\TextEntry::make('sisa_pembayaran')
                                ->label('Sisa Pembayaran')
                                ->state(function (\App\Models\Booking $record): float {
                                    $biayaSewa = $record->estimasi_biaya ?? 0;
                                    $biayaAntarJemput = $record->invoice?->pickup_dropOff ?? 0;
                                    $totalDenda = $record->penalty?->sum('amount') ?? 0;
                                    $dp = $record->invoice?->dp ?? 0;

                                    return ($biayaSewa + $biayaAntarJemput + $totalDenda) - $dp;
                                })
                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                            Infolists\Components\TextEntry::make('total_tagihan')
                                ->label('Total Tagihan')
                                ->state(function (\App\Models\Booking $record): float {
                                    $biayaSewa = $record->estimasi_biaya ?? 0;
                                    $biayaAntarJemput = $record->invoice?->pickup_dropOff ?? 0;
                                    $totalDenda = $record->penalty?->sum('amount') ?? 0;

                                    return $biayaSewa + $biayaAntarJemput + $totalDenda;
                                })
                                ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                ->size('lg')
                                ->weight('bold')
                                ->color('success'),
                        ]),
                    ]),

                Infolists\Components\Section::make('Informasi Mobil')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('car.carModel.brand.name')->label('Merek')->badge('success'),
                            Infolists\Components\TextEntry::make('car.carModel.name')
                                ->label('Model')
                                ->badge('success')
                                ->formatStateUsing(fn(string $state): string => Str::upper($state)),
                            Infolists\Components\TextEntry::make('car.nopol')->label('No. Polisi')->badge('success'),
                        ])
                    ]),

                Infolists\Components\Section::make('Informasi Pelanggan')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\TextEntry::make('customer.nama')->label('Nama Penyewa'),
                            Infolists\Components\TextEntry::make('customer.no_telp')->label('No. HP'),
                            Infolists\Components\TextEntry::make('customer.alamat')->label('Alamat'),
                        ])
                    ]),

                Infolists\Components\Section::make('Informasi Lokasi')
                    ->schema([
                        Infolists\Components\TextEntry::make('lokasi_pengantaran'),
                        Infolists\Components\TextEntry::make('lokasi_pengembalian'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                Tables\Columns\TextColumn::make('status')
                    ->badge()->alignCenter()
                    ->colors(['success' => 'aktif', 'info' => 'booking', 'gray' => 'selesai', 'danger' => 'batal'])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'aktif' => 'Aktif',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state)
                    }),
                Tables\Columns\TextColumn::make('car.nopol')->label('No Polisi')->alignCenter()->searchable(),
                TextColumn::make('car.carModel.name')->label('Nama Mobil')->searchable()->alignCenter()->wrap()->width(50),
                Tables\Columns\TextColumn::make('customer.nama')->label('Penyewa')->alignCenter()->searchable()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(250),

                Tables\Columns\TextColumn::make('tanggal_keluar')->label('Tgl Keluar')->date('d M Y')->alignCenter(),
                Tables\Columns\TextColumn::make('tanggal_kembali')->label('Tgl Kembali')->date('d M Y')->alignCenter(),
                Tables\Columns\TextColumn::make('estimasi_biaya')->label('Biaya')->alignCenter()->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'booking' => 'Booking',
                        'disewa' => 'Disewa',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                    ]),
                SelectFilter::make('garasi')
                    ->label('Garasi')
                    ->searchable()
                    ->options(
                        Car::query()
                            ->select('garasi')
                            ->distinct()
                            ->pluck('garasi', 'garasi')
                            ->toArray()
                    )
                    ->query(function (Builder $query, array $data) {
                        if (!$data['value']) {
                            return $query;
                        }

                        return $query->whereHas(
                            'car',
                            fn($q) =>
                            $q->where('garasi', $data['value'])
                        );
                    }),
                Filter::make('tanggal_keluar')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_keluar')
                            ->label('Tanggal Keluar'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal_keluar'],
                            fn(Builder $query, $date): Builder => $query->whereDate('tanggal_keluar', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['tanggal_keluar']) {
                            return null;
                        }
                        $date = Carbon::parse($data['tanggal_keluar'])->isoFormat('D MMM Y');
                        return "Tanggal Keluar: {$date}";
                    }),

                // -- PENAMBAHAN FILTER BARU DI SINI --
                Filter::make('tanggal_kembali')
                    ->form([
                        Forms\Components\DatePicker::make('tanggal_kembali')
                            ->label('Tanggal Kembali'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['tanggal_kembali'],
                            fn(Builder $query, $date): Builder => $query->whereDate('tanggal_kembali', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['tanggal_kembali']) {
                            return null;
                        }
                        $date = Carbon::parse($data['tanggal_kembali'])->isoFormat('D MMM Y');
                        return "Tanggal Kembali: {$date}";
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Detail Pesanan')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBookings::route('/'),
            'create' => Pages\CreateBooking::route('/create'),
            'view' => Pages\ViewBooking::route('/{record}'),
            'edit' => Pages\EditBooking::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()->where('status', 'booking')->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Booking yang belum diproses';
    }

    // -- KONTROL AKSES (superadmin, admin, staff) --

    public static function canViewAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin', 'supervisor']);
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        // Semua peran bisa masuk ke halaman edit, tetapi field akan dikontrol di dalam form
        return true;
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->isSuperAdmin(); // Hanya superadmin
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->isSuperAdmin(); // Hanya superadmin
    }
}
