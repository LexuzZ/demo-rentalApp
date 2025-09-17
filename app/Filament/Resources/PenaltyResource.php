<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenaltyResource\Pages;
use App\Filament\Resources\PenaltyResource\RelationManagers;
use App\Models\Penalty;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class PenaltyResource extends Resource
{
    protected static ?string $model = Penalty::class;

    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'Klaim Garasi';
    protected static ?string $navigationGroup = 'Transaksi';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('booking_id')
                    ->label('Booking')
                    ->relationship('booking', 'id')
                    ->getOptionLabelFromRecordUsing(function ($record) {
                        $tanggalKeluar = \Carbon\Carbon::parse($record->tanggal_keluar)->format('d M Y');
                        $tanggalKembali = \Carbon\Carbon::parse($record->tanggal_kembali)->format('d M Y');

                        return '#BK' . str_pad($record->id, 3, '0', STR_PAD_LEFT) .
                            ' - ' . $record->customer->nama .
                            ' - ' . $record->car->nopol .
                            ' - ' . $record->car->nama_mobil .
                            ', ' . $tanggalKeluar . ' s/d ' . $tanggalKembali;
                    })

                    ->required()
                    ->selectablePlaceholder(),


                Select::make('klaim')
                    ->label('Klaim Garasi')
                    ->options([
                        'baret' => 'Baret',
                        'bbm' => 'BBM',
                        'overtime' => 'Overtime',
                        'overland' => 'Overland',
                        'Washer' => 'Washer',
                        'no_penalty' => 'Tidak Ada Denda',
                    ])
                    ->default('no_penalty')
                    ->required(),

                Textarea::make('description')
                    ->label('Deskripsi')
                    ->rows(3),

                TextInput::make('amount')
                    ->label('Jumlah Denda (Rp)')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('booking.customer.nama')
                    ->label('Penyewa')
                    ->searchable()
                    ->wrap()
                    ->width(150),
                TextColumn::make('booking.car.carModel.name')
                    ->label('Mobil')
                    ->sortable()
                    ->wrap(),

                TextColumn::make('booking.car.nopol')
                    ->label('No. Polisi')
                    ->sortable()
                    ->alignCenter(),


                TextColumn::make('klaim')
                    ->label('Klaim Garasi')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'bbm',
                        'danger' => 'baret',
                        'warning' => 'overtime',
                        'info' => 'overland',
                        'primary' => 'washer',
                        'gray' => 'no_penalty',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'bbm' => 'BBM',
                        'overtime' => 'Overtime',
                        'baret' => 'Baret/Kerusakan',
                        'overland' => 'Overland',
                        'washer' => 'Washer/Cuci Mobil',
                        'no_penalty' => 'Tidak Ada Denda',
                        default => ucfirst($state),
                    }),
                Tables\Columns\TextColumn::make('amount')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))->label('Nominal')->alignCenter(),
                Tables\Columns\TextColumn::make('created_at')->date('d M Y')->alignCenter()->label('Dibuat Tgl'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('klaim')
                    ->label('Jenis Klaim')
                    ->options([
                        'baret' => 'Baret/Kerusakan',
                        'bbm' => 'BBM',
                        'overtime' => 'Overtime',
                        'overland' => 'Overland',
                        'washer' => 'Washer/Cuci Mobil',
                        'no_penalty' => 'Tidak Ada Denda',
                    ])
                    ->searchable(),
            ])->actions([
                    Tables\Actions\EditAction::make()
                        ->label('')
                        ->tooltip('Edit Klaim Garasi')
                        ->icon('heroicon-o-pencil')
                        ->color('warning')
                        ->hiddenLabel()
                        ->button(),
                    Tables\Actions\DeleteAction::make()
                        ->label('')
                        ->tooltip('Hapus Klaim Garasi')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->hiddenLabel()
                        ->button(),
                ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPenalties::route('/'),
            'create' => Pages\CreatePenalty::route('/create'),
            'edit' => Pages\EditPenalty::route('/{record}/edit'),
        ];
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
