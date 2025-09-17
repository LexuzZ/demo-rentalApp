<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DriverResource\Pages;
use App\Filament\Resources\DriverResource\RelationManagers;
use App\Models\Driver;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class DriverResource extends Resource
{
    protected static ?string $model = Driver::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationGroup = 'Data';
    protected static ?string $label = 'Staff';
    protected static ?string $pluralLabel = 'Data Staff';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required(),

                TextInput::make('no_telp')
                    ->label('No HP')
                    ->tel()
                    ->required(),

                Select::make('status')
                    ->label('Status')
                    ->options([
                        'tersedia' => 'Tersedia',
                        'mengemudi' => 'Sedang Mengemudi',
                    ])
                    ->default('tersedia')
                    ->required(),

                TextInput::make('harga')
                    ->label('Harga Jasa')
                    ->numeric()
                    ->prefix('Rp')
                    ->required(),
            ]),
        ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('nama')->label('Nama')
                ->searchable()
                ->wrap()
                ->width(150)
                ->sortable(),
                TextColumn::make('no_telp')->label('No HP'),
                BadgeColumn::make('status')->label('Status')->colors([
                    'success' => 'tersedia',
                    'warning' => 'mengemudi',
                ]),
                TextColumn::make('harga')->label('Harga')->money('IDR'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Edit Data Staff')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus Data Staff')
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
            'index' => Pages\ListDrivers::route('/'),
            'create' => Pages\CreateDriver::route('/create'),
            'edit' => Pages\EditDriver::route('/{record}/edit'),
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
