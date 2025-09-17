<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PaymentResource\Pages;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Pembayaran';
    protected static ?string $pluralLabel = 'Riwayat Pembayaran';

    /**
     * Memodifikasi data sebelum form edit diisi.
     */
    public static function mutateFormDataBeforeFill(array $data): array
    {
        $invoiceId = $data['invoice_id'] ?? null;

        if ($invoiceId) {
            $invoice = Invoice::with('booking.penalty')->find($invoiceId);
            if ($invoice) {
                $biayaSewa = $invoice->booking?->estimasi_biaya ?? 0;
                $biayaAntarJemput = $invoice->pickup_dropOff ?? 0;
                $totalDenda = $invoice->booking?->penalty->sum('amount') ?? 0;

                $data['pembayaran'] = $biayaSewa + $biayaAntarJemput + $totalDenda;
            }
        }

        return $data;
    }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('invoice_id')
                    ->label('Faktur')
                    ->relationship('invoice', 'id', fn($query) => $query->with(['booking.customer', 'booking.penalty']))
                    ->getOptionLabelFromRecordUsing(fn($record) => 'INV #' . $record->id . ' - ' . $record->booking->customer->nama)
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function (Forms\Set $set, ?string $state) {
                        $invoice = Invoice::with('booking.penalty')->find($state);

                        $biayaSewa = $invoice?->booking?->estimasi_biaya ?? 0;
                        $biayaAntarJemput = $invoice?->pickup_dropOff ?? 0;
                        $totalDenda = $invoice?->booking?->penalty->sum('amount') ?? 0;

                        $set('pembayaran', $biayaSewa + $biayaAntarJemput + $totalDenda);
                    }),
                Forms\Components\DatePicker::make('tanggal_pembayaran')
                    ->required()
                    ->default(now()),
                Forms\Components\Select::make('metode_pembayaran')
                    ->options(['tunai' => 'Tunai', 'transfer' => 'Transfer', 'qris' => 'QRIS'])
                    ->required(),
                Forms\Components\TextInput::make('pembayaran')
                    ->label('Jumlah Pembayaran')
                    ->numeric()
                    ->prefix('Rp')
                    ->required()
                    ->readOnly(),
                Forms\Components\Select::make('status')
                    ->options(['lunas' => 'Lunas', 'belum_lunas' => 'Belum Lunas'])
                    ->default('belum_lunas')
                    ->required()
                    // -- PERBAIKAN DI SINI --
                    // Sembunyikan di halaman 'create'
                    ->hidden(fn(string $operation): bool => $operation === 'create')
                    // Di halaman 'edit', nonaktifkan jika bukan superadmin
                    ->disabled(fn(string $operation): bool => $operation === 'edit' && !Auth::user()->isSuperAdmin()),
            ])
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                // Tables\Columns\TextColumn::make('invoice.id')->label('Faktur'),
                Tables\Columns\TextColumn::make('invoice.booking.customer.nama')->label('Penyewa')->searchable()->alignCenter()->wrap() // <-- Tambahkan wrap agar teks turun
                    ->width(150),
                TextColumn::make('tanggal_pembayaran')
                    ->label('Tgl Pembayaran')
                    ->searchable()
                    ->date('d M Y')->alignCenter(),
                Tables\Columns\TextColumn::make('total_bayar')
                    ->label('Jumlah Bayar')
                    ->alignCenter()
                    ->getStateUsing(function ($record) {
                        $invoice = $record->invoice;
                        $biayaSewa = $invoice?->booking?->estimasi_biaya ?? 0;
                        $biayaAntarJemput = $invoice?->pickup_dropOff ?? 0;
                        $totalDenda = $invoice?->booking?->penalty->sum('amount') ?? 0;

                        return $biayaSewa + $biayaAntarJemput + $totalDenda;
                    })
                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ,
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'lunas',
                        'danger' => 'belum_lunas',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('metode_pembayaran')
                    ->label('Metode')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'tunai',
                        'info' => 'transfer',
                        'gray' => 'qris',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'lunas' => 'Lunas',
                        'belum_lunas' => 'Belum Lunas',
                    ]),
                SelectFilter::make('metode_pembayaran')
                    ->label('Metode')
                    ->options([
                        'tunai' => 'Tunai',
                        'transfer' => 'Transfer',
                        'qris' => 'QRIS',
                    ]),

                Filter::make('tanggal_pembayaran')
                    ->form([
                        DatePicker::make('date')
                            ->label('Tanggal Pembayaran'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['date'],
                            fn(Builder $q, $date) => $q->whereDate('tanggal_pembayaran', $date)
                        );
                    })
                    ->indicateUsing(function (array $data): ?string {
                        return $data['date']
                            ? 'Tanggal Pembayaran: ' . \Carbon\Carbon::parse($data['date'])->isoFormat('D MMMM Y')
                            : null;
                    }),
                Filter::make('tanggal_pembayaran')
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\Select::make('month')
                                    ->label('Bulan')
                                    ->options(array_reduce(range(1, 12), function ($carry, $month) {
                                        $carry[$month] = Carbon::create(null, $month)->locale('id')->isoFormat('MMMM');
                                        return $carry;
                                    }, []))
                                    ->default(now()->month), // ✅ default bulan ini
                                Forms\Components\Select::make('year')
                                    ->label('Tahun')
                                    ->options(function () {
                                        $years = range(now()->year, now()->year - 5);
                                        return array_combine($years, $years);
                                    })
                                    ->default(now()->year), // ✅ default tahun ini
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['month'], fn(Builder $query, $month): Builder => $query->whereMonth('tanggal_pembayaran', $month))
                            ->when($data['year'], fn(Builder $query, $year): Builder => $query->whereYear('tanggal_pembayaran', $year));
                    })
                    ->indicateUsing(function (array $data): ?string {
                        if (!$data['month'] && !$data['year']) {
                            return null;
                        }
                        $monthName = $data['month'] ? Carbon::create()->month((int) $data['month'])->isoFormat('MMMM') : '';
                        return 'Periode: ' . $monthName . ' ' . $data['year'];
                    })
                    ->columnSpan(2)->columns(2),

            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\Action::make('detailPembayaran')
                    ->label('')
                    ->tooltip('Detail Pembayaran')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button()
                    ->infolist([
                        \Filament\Infolists\Components\Section::make('Detail Faktur')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('invoice.id')
                                    ->label('No. Faktur')
                                    ->formatStateUsing(fn($state) => 'INV #' . $state),

                                \Filament\Infolists\Components\TextEntry::make('invoice.booking.customer.nama')
                                    ->label('Penyewa'),

                                \Filament\Infolists\Components\TextEntry::make('invoice.booking.car.nopol')
                                    ->label('No. Polisi'),

                                \Filament\Infolists\Components\TextEntry::make('invoice.booking.car.carModel.name')
                                    ->label('Mobil'),
                            ])
                            ->columns(2),

                        \Filament\Infolists\Components\Section::make('Rincian Biaya')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('biaya_sewa')
                                    ->label('Biaya Sewa')
                                    ->state(function ($record) {
                                        return $record->invoice->booking?->estimasi_biaya ?? 0;
                                    })
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                                \Filament\Infolists\Components\TextEntry::make('biaya_antar')
                                    ->label('Biaya Antar/Jemput')
                                    ->state(fn($record) => $record->invoice->pickup_dropOff ?? 0)
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                                \Filament\Infolists\Components\TextEntry::make('total_denda')
                                    ->label('Total Denda')
                                    ->state(fn($record) => $record->invoice->booking?->penalty->sum('amount') ?? 0)
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),

                                \Filament\Infolists\Components\TextEntry::make('total')
                                    ->label('Total Tagihan')
                                    ->state(function ($record) {
                                        $biayaSewa = $record->invoice->booking?->estimasi_biaya ?? 0;
                                        $biayaAntar = $record->invoice->pickup_dropOff ?? 0;
                                        $totalDenda = $record->invoice->booking?->penalty->sum('amount') ?? 0;
                                        return $biayaSewa + $biayaAntar + $totalDenda;
                                    })
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                    ->weight(\Filament\Support\Enums\FontWeight::Bold),
                            ])
                            ->columns(2),

                        \Filament\Infolists\Components\Section::make('Pembayaran')
                            ->schema([
                                \Filament\Infolists\Components\TextEntry::make('status')
                                    ->badge()
                                    ->colors([
                                        'success' => 'lunas',
                                        'danger' => 'belum_lunas',
                                    ])
                                    ->formatStateUsing(fn($state) => $state === 'lunas' ? 'Lunas' : 'Belum Lunas'),

                                \Filament\Infolists\Components\TextEntry::make('metode_pembayaran')
                                    ->label('Metode')
                                    ->badge()
                                    ->colors([
                                        'success' => 'tunai',
                                        'info' => 'transfer',
                                        'gray' => 'qris',
                                    ])
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'tunai' => 'Tunai',
                                        'transfer' => 'Transfer',
                                        'qris' => 'QRIS',
                                        default => ucfirst($state),
                                    }),

                                \Filament\Infolists\Components\TextEntry::make('tanggal_pembayaran')
                                    ->label('Tanggal Pembayaran')
                                    ->date('d M Y'),
                            ])
                            ->columns(3),
                    ])->modalCancelActionLabel('Tutup'), // ✅ tombol cancel di bawah,
                Tables\Actions\Action::make('markAsPaid')
                    ->label('') // biar icon aja
                    ->tooltip('Tandai Lunas')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->button()
                    ->hiddenLabel()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalSubheading('Apakah kamu yakin ingin menandai pembayaran ini sebagai **Lunas**?')
                    ->modalIcon('heroicon-o-currency-dollar') // bisa ganti sesuai icon
                    ->requiresConfirmation()
                    ->action(function (Payment $record) {
                        $record->update(['status' => 'lunas']);
                    })
                    ->visible(
                        fn(Payment $record): bool =>
                        $record->status === 'belum_lunas'
                        && Auth::user()?->role === 'superadmin'
                    ),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus Pembayaran')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->hiddenLabel()
                    ->button(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPayments::route('/'),
            'create' => Pages\CreatePayment::route('/create'),
            'edit' => Pages\EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('status', 'belum_lunas')
            ->count();
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Pembayaran yang belum lunas';
    }

    // -- KONTROL AKSES BARU (superadmin, admin, staff) --

    public static function canViewAny(): bool
    {
        return true;
    }

    public static function canCreate(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        return Auth::user()->isSuperAdmin();
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->isSuperAdmin();
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
