<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvoiceResource\Pages;
use App\Filament\Resources\InvoiceResource\RelationManagers;
use App\Models\Invoice;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InvoiceResource extends Resource
{
    protected static ?string $model = Invoice::class;

    protected static ?string $navigationIcon = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Transaksi';
    protected static ?string $label = 'Faktur';
    protected static ?string $pluralLabel = 'Faktur Sewa';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\Grid::make(2)->schema([
                Forms\Components\Select::make('booking_id')
                    ->label('Booking')
                    ->relationship('booking', 'id', fn($query) => $query->with('car', 'customer'))
                    ->getOptionLabelFromRecordUsing(
                        fn($record) =>
                        $record->id . ' - ' . $record->car->nopol . ' (' . $record->customer->nama . ')'
                    )
                    ->selectablePlaceholder()
                    ->required()
                    ->searchable()
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $booking = \App\Models\Booking::find($state);
                        $estimasi = $booking?->estimasi_biaya ?? 0;
                        $pickup = $get('pickup_dropOff') ?? 0;
                        $total = $estimasi + $pickup;
                        $set('total', $total);
                        $set('dp', 0);
                        $set('sisa_pembayaran', $total);
                    }),

                Forms\Components\DatePicker::make('tanggal_invoice')
                    ->label('Tanggal Invoice')
                    ->required(),

                Forms\Components\TextInput::make('dp')
                    ->label('Uang Muka')
                    ->prefix('Rp')
                    ->numeric()
                    ->default(0)
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set, Forms\Get $get) {
                        $total = $get('total') ?? 0;
                        $set('sisa_pembayaran', max($total - $state, 0));
                    }),

                Forms\Components\TextInput::make('pickup_dropOff')
                    ->label('Biaya Pengantaran')
                    ->live()
                    ->required()
                    ->prefix('Rp')
                    ->numeric()
                    ->default(0),

                Forms\Components\TextInput::make('sisa_pembayaran')
                    ->label('Sisa Pembayaran')
                    ->prefix('Rp')
                    ->numeric()
                    ->readOnly()
                    ->default(0),

                Forms\Components\TextInput::make('total')
                    ->label('Total Biaya')
                    ->prefix('Rp')
                    ->numeric()
                    ->readOnly()
                    ->required(),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('')
                    ->schema([
                        Infolists\Components\Actions::make([
                            Infolists\Components\Actions\Action::make('addPayment')
                                ->label('Tambah Pembayaran')
                                ->icon('heroicon-o-banknotes')
                                ->color('success')
                                ->visible(fn(Invoice $record) => $record && !$record->payment)
                                ->url(fn(Invoice $record) => PaymentResource::getUrl('create', ['invoice_id' => $record->id])),
                            Infolists\Components\Actions\Action::make('viewPayment')
                                ->label('Edit Pembayaran')
                                ->icon('heroicon-o-eye')
                                ->color('gray')
                                ->visible(fn(Invoice $record) => $record->payment)
                                ->url(fn(Invoice $record) => PaymentResource::getUrl('edit', ['record' => $record->payment->id])),
                            Infolists\Components\Actions\Action::make('copyInvoice')
                                ->label('Copy Faktur')
                                ->icon('heroicon-o-clipboard-document')
                                ->color('gray')
                                ->modalHeading('Salin Detail Faktur')
                                ->modalContent(function (Invoice $record): View {
                                    // Hitung total & denda
                                    $totalDenda = $record->booking?->penalty->sum('amount') ?? 0;
                                    $totalTagihan = ($record->booking?->estimasi_biaya ?? 0) + ($record->pickup_dropOff ?? 0) + $totalDenda;
                                    $sisaPembayaran = $totalTagihan - ($record->dp ?? 0);

                                    // Detail mobil & tanggal
                                    $carDetails = "{$record->booking->car->carModel->brand->name} {$record->booking->car->carModel->name} ({$record->booking->car->nopol})";
                                    $tglKeluar = \Carbon\Carbon::parse($record->booking->tanggal_keluar)->isoFormat('D MMMM Y');
                                    $tglKembali = \Carbon\Carbon::parse($record->booking->tanggal_kembali)->isoFormat('D MMMM Y');

                                    // Text yang akan dicopy
                                    $textToCopy = "Halo *{$record->booking->customer->nama}* 👋😊\n\n";
                                    $textToCopy .= "Berikut detail faktur sewa mobil Anda dari *Semeton Pesiar*:\n\n";
                                    $textToCopy .= "🧾 *No. Faktur:* #{$record->id}\n";
                                    $textToCopy .= "📅 *Tanggal:* " . \Carbon\Carbon::parse($record->tanggal_invoice)->isoFormat('D MMMM Y') . "\n";
                                    $textToCopy .= "-----------------------------------\n";
                                    $textToCopy .= "🚗 *Mobil:* {$carDetails}\n";
                                    $textToCopy .= "⏳ *Durasi:* {$tglKeluar} - {$tglKembali} ({$record->booking->total_hari} hari)\n";
                                    $textToCopy .= "💰 *Biaya Sewa:* Rp " . number_format($record->booking->estimasi_biaya, 0, ',', '.') . "\n";
                                    if ($record->pickup_dropOff > 0) {
                                        $textToCopy .= "➡️⬅️ *Biaya Antar/Jemput:* Rp " . number_format($record->pickup_dropOff, 0, ',', '.') . "\n";
                                    }
                                    if ($totalDenda > 0) {
                                        $textToCopy .= "⚖️ *Denda/Klaim Garasi:* Rp " . number_format($totalDenda, 0, ',', '.') . "\n";
                                    }
                                    $textToCopy .= "-----------------------------------\n";
                                    $textToCopy .= "✉️ *Total Tagihan:* Rp " . number_format($totalTagihan, 0, ',', '.') . "\n";
                                    $textToCopy .= "🔐 *Uang Muka (DP):* Rp " . number_format($record->dp, 0, ',', '.') . "\n";
                                    $textToCopy .= "🔔 *Sisa Pembayaran:* *Rp " . number_format($sisaPembayaran, 0, ',', '.') . "*\n\n";
                                    $textToCopy .= "Mohon lakukan pembayaran ke salah satu rekening berikut:\n";
                                    $textToCopy .= "🏦 Mandiri: 1610006892835 a.n. ACHMAD MUZAMMIL\n";
                                    $textToCopy .= "🏦 BCA: 2320418758 a.n. SRI NOVYANA\n\n";
                                    $textToCopy .= "🙏 Terima kasih.";

                                    return view('filament.actions.copy-invoice', [
                                        'textToCopy' => $textToCopy,
                                    ]);
                                })
                                ->modalSubmitAction(false)
                                ->modalCancelAction(false),


                            Infolists\Components\Actions\Action::make('download')
                                ->label('Unduh PDF')
                                ->icon('heroicon-o-arrow-down-tray')
                                ->color('gray')
                                ->url(fn(Invoice $record) => route('invoices.pdf.download', $record))
                                ->openUrlInNewTab(),
                            Infolists\Components\Actions\Action::make('sendWhatsapp')
                                ->label('Faktur via WA')
                                ->icon('heroicon-o-chat-bubble-left-right')
                                ->color('success')
                                ->url(function (Invoice $record) {
                                    $phone = $record->booking->customer->no_telp;
                                    $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
                                    if (substr($cleanedPhone, 0, 1) === '0') {
                                        $cleanedPhone = '62' . substr($cleanedPhone, 1);
                                    }

                                    // Menghitung total
                                    $totalDenda = $record->booking?->penalty->sum('amount') ?? 0;
                                    $totalTagihan = $record->booking?->estimasi_biaya + $record->pickup_dropOff + $totalDenda;
                                    $sisaPembayaran = $totalTagihan - $record->dp;

                                    // Mengambil detail mobil dan tanggal
                                    $carDetails = "{$record->booking->car->carModel->brand->name} {$record->booking->car->carModel->name} ({$record->booking->car->nopol})";
                                    $tglKeluar = \Carbon\Carbon::parse($record->booking->tanggal_keluar)->format('d M Y');
                                    $tglKembali = \Carbon\Carbon::parse($record->booking->tanggal_kembali)->format('d M Y');
                                    $totalHari = $record->booking->total_hari;

                                    // Membuat template pesan yang lebih detail
                                    $message = "Halo 👋😊 *{$record->booking->customer->nama}*,\n\n";
                                    $message .= "Berikut kami kirimkan detail faktur sewa mobil Anda dari *Semeton Pesiar*:\n\n";
                                    $message .= "🧾 *No. Faktur:* #{$record->id}\n";
                                    $message .= "📅 *Tanggal:* " . \Carbon\Carbon::parse($record->tanggal_invoice)->format('d F Y') . "\n";
                                    $message .= "-----------------------------------\n";
                                    $message .= "📜 *Rincian Sewa:*\n";
                                    $message .= "🚗 • *Mobil:* {$carDetails}\n";
                                    $message .= "⏳ • *Durasi:* {$tglKeluar} - {$tglKembali} ({$totalHari} hari)\n";
                                    $message .= "🗓️ • *Biaya Sewa Harian:* Rp " . number_format($record->booking->harga_harian, 0, ',', '.') . "\n";
                                    $message .= "💰 • *Total Biaya Sewa:* Rp " . number_format($record->booking->estimasi_biaya, 0, ',', '.') . "\n";
                                    if ($record->pickup_dropOff > 0) {
                                        $message .= "• ➡️⬅️ *Biaya Antar/Jemput:* Rp " . number_format($record->pickup_dropOff, 0, ',', '.') . "\n";
                                    }
                                    if ($totalDenda > 0) {
                                        $message .= "• ⚖️ *Total Klaim Garasi:* Rp " . number_format($totalDenda, 0, ',', '.') . "\n";
                                    }
                                    $message .= "-----------------------------------\n";
                                    $message .= "✉️ *Total Tagihan:* Rp " . number_format($totalTagihan, 0, ',', '.') . "\n";
                                    $message .= "🔐 *Uang Muka (DP):* - Rp " . number_format($record->dp, 0, ',', '.') . "\n";
                                    $message .= "🔔 *Sisa Pembayaran:* *Rp " . number_format($sisaPembayaran, 0, ',', '.') . "*\n\n";
                                    $message .= "Mohon lakukan sisa pembayaran ke salah satu rekening berikut:\n";
                                    $message .= "*- Mandiri:* 1610006892835 (a.n. ACHMAD MUZAMMIL)\n";
                                    $message .= "*- BCA:* 2320418758 (a.n. SRI NOVYANA)\n\n";
                                    $message .= "Terima kasih 🙏";

                                    return 'https://wa.me/' . $cleanedPhone . '?text=' . urlencode($message);
                                })
                                ->openUrlInNewTab(),

                        ]),
                    ]),
                Infolists\Components\Section::make('Rincian Biaya')
                    ->schema([
                        Infolists\Components\Grid::make(3)->schema([
                            Infolists\Components\Grid::make(3)->schema([
                                Infolists\Components\TextEntry::make('booking.estimasi_biaya')->label('Biaya Sewa')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                Infolists\Components\TextEntry::make('pickup_dropOff')->label('Biaya Antar/Jemput')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                Infolists\Components\TextEntry::make('total_denda')
                                    ->label('Total Denda')
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                    ->state(fn(Invoice $record) => $record->booking?->penalty->sum('amount') ?? 0),
                                Infolists\Components\TextEntry::make('dp')->label('Uang Muka (DP)')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.')),
                                Infolists\Components\TextEntry::make('sisa_pembayaran')
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                    ->state(function (Invoice $record): float {
                                        $biayaSewa = $record->booking?->estimasi_biaya ?? 0;
                                        $biayaAntarJemput = $record->pickup_dropOff ?? 0;
                                        $totalDenda = $record->booking?->penalty->sum('amount') ?? 0;
                                        $totalTagihan = $biayaSewa + $biayaAntarJemput + $totalDenda;
                                        $dp = $record->dp ?? 0;
                                        return $totalTagihan - $dp;
                                    }),
                                Infolists\Components\TextEntry::make('total')
                                    ->label('Total Tagihan')
                                    ->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                                    ->size('lg')
                                    ->weight('bold')
                                    ->state(function (Invoice $record): float {
                                        $biayaSewa = $record->booking?->estimasi_biaya ?? 0;
                                        $biayaAntarJemput = $record->pickup_dropOff ?? 0;
                                        $totalDenda = $record->booking?->penalty->sum('amount') ?? 0;
                                        return $biayaSewa + $biayaAntarJemput + $totalDenda;
                                    }),
                            ]),
                        ]),
                        Infolists\Components\Section::make('Informasi Terkait')
                            ->schema([
                                Infolists\Components\Grid::make(3)->schema([
                                    Infolists\Components\TextEntry::make('id')->label('ID Faktur'),
                                    Infolists\Components\TextEntry::make('booking.id')->label('ID Booking'),
                                    Infolists\Components\TextEntry::make('tanggal_invoice')->date('d M Y'),
                                    Infolists\Components\TextEntry::make('booking.customer.nama')->label('Pelanggan'),
                                    Infolists\Components\TextEntry::make('booking.car.carModel.name')->label('Mobil'),
                                    Infolists\Components\TextEntry::make('booking.car.nopol')->label('No. Polisi'),
                                ])
                            ]),
                    ])
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table->recordUrl(null)->columns([
            // TextColumn::make('id')->label('ID Faktur')->searchable(),
            TextColumn::make('booking.customer.nama')->label('Penyewa')->searchable()->wrap()->width(150),
            TextColumn::make('booking.car.nopol')->label('Mobil')->searchable(),
            textColumn::make('dp')->label('Uang Muka (DP)')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))->color('success'),
            TextColumn::make('sisa_pembayaran')->label('Sisa Bayar')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))
                ->state(function (Invoice $record): float {
                    $biayaSewa = $record->booking?->estimasi_biaya ?? 0;
                    $biayaAntarJemput = $record->pickup_dropOff ?? 0;
                    $totalDenda = $record->booking?->penalty->sum('amount') ?? 0;
                    $totalTagihan = $biayaSewa + $biayaAntarJemput + $totalDenda;
                    $dp = $record->dp ?? 0;
                    return $totalTagihan - $dp;
                })->color('danger'),
            TextColumn::make('total')->label('Total Tagihan')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))->state(function (Invoice $record): float {
                $biayaSewa = $record->booking?->estimasi_biaya ?? 0;
                $biayaAntarJemput = $record->pickup_dropOff ?? 0;
                $totalDenda = $record->booking?->penalty->sum('amount') ?? 0;
                return $biayaSewa + $biayaAntarJemput + $totalDenda;
            }),
            // TextColumn::make('tanggal_invoice')->label('Tanggal')->date('d M Y'),
        ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('')
                    ->tooltip('Detail Faktur')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Ubah Faktur')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
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
            'index' => Pages\ListInvoices::route('/'),
            'create' => Pages\CreateInvoice::route('/create'),
            'view' => Pages\ViewInvoice::route('/{record}'), // <-- Daftarkan halaman view
            'edit' => Pages\EditInvoice::route('/{record}/edit'),
        ];
    }
    public static function canViewAny(): bool
    {
        // Semua peran bisa melihat daftar mobil
        return true;
    }

    public static function canCreate(): bool
    {
        // Hanya superadmin dan admin yang bisa membuat data baru
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canEdit(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa mengedit
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDelete(Model $record): bool
    {
        // Hanya superadmin dan admin yang bisa menghapus
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        // Hanya superadmin dan admin yang bisa hapus massal
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
