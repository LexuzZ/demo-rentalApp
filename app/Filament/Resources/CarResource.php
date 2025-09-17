<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CarResource\BookingsResource\RelationManagers\BookingsRelationManager;
use App\Filament\Resources\CarResource\Pages; // <-- Pastikan ini di-import
use App\Filament\Resources\CarResource\RelationManagers\ServiceHistoriesRelationManager;
use App\Filament\Resources\CarResource\RelationManagers\TempoRelationManager;
use App\Models\Car;
use App\Models\CarModel;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Forms\Components\{TextInput, Select, FileUpload, Grid, DatePicker, DateTimePicker, TimePicker};
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\{TextColumn, ImageColumn};
use Filament\Tables\Filters\Filter;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class CarResource extends Resource
{
    protected static ?string $model = Car::class;

    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Manajemen Mobil';
    protected static ?string $label = 'Mobil';
    protected static ?string $pluralLabel = 'Data Mobil';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form
            ->schema([
                Grid::make(2)->schema([
                    Select::make('brand_id')
                        ->label('Merek')
                        ->relationship(name: 'carModel.brand', titleAttribute: 'name')
                        ->searchable()
                        ->preload()
                        ->live()
                        ->afterStateUpdated(fn(Forms\Set $set) => $set('car_model_id', null))
                        ->dehydrated(false),

                    Select::make('car_model_id')
                        ->label('Nama Mobil')
                        ->options(
                            fn(Forms\Get $get): array => CarModel::query()
                                ->where('brand_id', $get('brand_id'))
                                ->pluck('name', 'id')->all()
                        )
                        ->searchable()
                        ->preload()
                        ->live()
                        ->required(),

                    TextInput::make('nopol')
                        ->label('Nomor Polisi')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->validationMessages([
                            'unique' => 'Nomor polisi ini sudah terdaftar di sistem.',
                        ]),

                    TextInput::make('year')
                        ->label('Tahun')
                        ->numeric()
                        ->minValue(2000)
                        ->maxValue(date('Y') + 1)
                        ->required(),

                    TextInput::make('garasi')
                        ->label('Garasi')
                        ->required(),

                    TextInput::make('warna')
                        ->label('Warna Mobil')
                        ->required(),

                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'ready' => 'Ready',
                            'disewa' => 'Disewa',
                            'perawatan' => 'Perawatan',
                            'nonaktif' => 'Nonaktif',
                        ])
                        ->default('ready')
                        ->required(),

                    Select::make('transmisi')
                        ->label('Transmisi')
                        ->options([
                            'matic' => 'Matic',
                            'manual' => 'Manual',
                        ])
                        ->default('matic')
                        ->required(),

                    TextInput::make('harga_harian')
                        ->label('Harga Sewa Harian')
                        ->numeric()
                        ->required()
                        ->prefix('Rp'),

                    TextInput::make('harga_pokok')
                        ->label('Harga Pokok')
                        ->numeric()
                        ->required()
                        ->prefix('Rp'),

                    TextInput::make('harga_bulanan')
                        ->label('Harga Sewa Bulanan')
                        ->numeric()
                        ->prefix('Rp'),


                    FileUpload::make('photo')
                        ->label('Foto Mobil')
                        ->image()
                        ->directory('cars')
                        ->imagePreviewHeight('150')
                        ->loadingIndicatorPosition('left')
                        // ->panelAspectRatio('2:1')
                        ->panelLayout('integrated')
                        ->disk('public')
                        ->visibility('public')
                        ->columnSpanFull(),
                ]),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                // ImageColumn::make('photo')
                //     ->label('Foto Mobil')
                //     ->disk('public')
                //     ->circular(),
                TextColumn::make('nopol')->label('Nopol')->sortable()->searchable(),
                TextColumn::make('carModel.name')->label('Nama Mobil')->sortable()->searchable()->alignCenter(),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->alignCenter()
                    ->colors([
                        'success' => 'ready',
                        'info' => 'disewa',
                        'danger' => 'perawatan',
                        'gray' => 'nonaktif',
                    ])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'ready' => 'Ready',
                        'disewa' => 'Disewa',
                        'perawatan' => 'Maintenance',
                        'nonaktif' => 'Nonaktif',
                        default => ucfirst($state),
                    }),
                TextColumn::make('harga_harian')->label('Harian')->formatStateUsing(fn($state) => 'Rp ' . number_format($state, 0, ',', '.'))->alignCenter(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                Filter::make('availability')
                    ->label('Cek Ketersediaan Mobil')
                    ->form([
                        Grid::make(2)->schema([
                            DatePicker::make('start_date')
                                ->label('Dari Tanggal')
                                ->required(),
                            TimePicker::make('start_time')
                                ->label('Waktu Keluar')
                                ->required()
                                ->withoutSeconds(), // Opsional
                        ]),
                        Grid::make(2)->schema([
                            DatePicker::make('end_date')
                                ->label('Sampai Tanggal')
                                ->required(),
                            TimePicker::make('end_time')
                                ->label('Waktu Kembali')
                                ->required()
                                ->withoutSeconds(), // Opsional
                        ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        // Pastikan semua data ada sebelum diproses
                        if (empty($data['start_date']) || empty($data['end_date']) || empty($data['start_time']) || empty($data['end_time'])) {
                            return $query;
                        }

                        // Gabungkan tanggal dan waktu menjadi satu objek Carbon
                        $startDateTime = Carbon::parse($data['start_date'] . ' ' . $data['start_time']);
                        $endDateTime = Carbon::parse($data['end_date'] . ' ' . $data['end_time']);

                        return $query
                            ->whereNotIn('status', ['perawatan', 'nonaktif'])
                            ->whereDoesntHave('bookings', function (Builder $bookingQuery) use ($startDateTime, $endDateTime) {
                                $bookingQuery->where(function (Builder $q) use ($startDateTime, $endDateTime) {
                                    // Cek tumpang tindih waktu
                                    $q->where(function (Builder $subQ) use ($startDateTime, $endDateTime) {
                                        $subQ->where('tanggal_keluar', '<', $endDateTime)
                                            ->where('tanggal_kembali', '>', $startDateTime);
                                    });
                                });
                            });
                    }),
            ])
            ->headerActions([
                Action::make('copyList')
                    ->label('Copy Daftar Mobil')
                    ->icon('heroicon-o-clipboard-document')
                    ->color('gray')
                    ->visible(function (Pages\ListCars $livewire): bool {
                        $filters = $livewire->tableFilters;
                        return !empty($filters['availability']['start_date']) && !empty($filters['availability']['end_date']);
                    })
                    ->modalContent(function (Pages\ListCars $livewire): View {
                        $cars = $livewire->getFilteredTableQuery()
                            ->where('garasi', 'DEMO')
                            ->with('carModel')
                            ->get()
                            ->sortBy(fn($car) => $car->carModel->name, SORT_NATURAL | SORT_FLAG_CASE)
                            ->values();
                        $filters = $livewire->tableFilters;
                        $startDateTime = \Carbon\Carbon::parse($filters['availability']['start_date'] . ' ' . $filters['availability']['start_time'])->locale('id')->isoFormat('D MMMM Y, HH:mm');
                        $endDateTime = \Carbon\Carbon::parse($filters['availability']['end_date'] . ' ' . $filters['availability']['end_time'])->locale('id')->isoFormat('D MMMM Y, HH:mm');

                        $textToCopy = "Halo,✋ Lombok 😊\nMobil yang tersedia di Garasi Semeton Pesiar periode *{$startDateTime}* sampai *{$endDateTime}* :\n\n";
                        foreach ($cars as $index => $car) {
                            $textToCopy .= ($index + 1) . ". *{$car->carModel->brand->name} {$car->carModel->name}* {$car->nopol} ✅\n";
                        }
                        $textToCopy .= "\nInfo lebih lanjut bisa hubungi kami. Terima kasih.\n\n📞  WA: 081907367197\n🌐  Website: www.semetonpesiar.com";

                        return view('filament.actions.copy-car-list', ['textToCopy' => $textToCopy]);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelAction(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Edit Mobil')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus Mobil')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\ViewAction::make()
                    ->label('') // kosongkan label
                    ->tooltip('Detail Mobil')
                    ->icon('heroicon-o-eye')
                    ->color('info')
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
            'index' => Pages\ListCars::route('/'),
            'create' => Pages\CreateCar::route('/create'),
            'edit' => Pages\EditCar::route('/{record}/edit'),
            'view' => Pages\ViewCar::route('/{record}'),
        ];
    }
    public static function getRelations(): array
    {
        return [
            BookingsRelationManager::class,
            ServiceHistoriesRelationManager::class,
        ];
    }
    public static function getWidgets(): array
    {
        return [
            //
        ];
    }
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::query()
            ->where('status', 'ready')
            ->count();
    }
    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Mobil yang siap disewa';
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
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }

    public static function canDeleteAny(): bool
    {
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
    public static function canAccess(): bool
    {
        // Hanya pengguna dengan peran 'admin' yang bisa melihat halaman ini
        return Auth::user()->hasAnyRole(['superadmin', 'admin']);
    }
}
