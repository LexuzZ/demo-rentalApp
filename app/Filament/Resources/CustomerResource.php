<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CustomerResource\Pages;
use App\Filament\Resources\CustomerResource\RelationManagers;
use App\Models\Customer;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class CustomerResource extends Resource
{
    protected static ?string $model = Customer::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Data';
    protected static ?string $label = 'Penyewa';
    protected static ?string $pluralLabel = 'Data Penyewa';

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Grid::make(2)->schema([
                TextInput::make('nama')
                    ->label('Nama')
                    ->required(),
                TextInput::make('no_telp')
                    ->label('No HP / WhatsApp')
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->dehydrateStateUsing(fn(string $state): string => preg_replace('/[^0-9]/', '', $state))
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('ktp')
                    ->label('No KTP')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('lisence')
                    ->label('no SIM')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->nullable(),
                FileUpload::make('identity_file')
                    ->label('Upload KTP')
                    ->disk('public')
                    ->directory('identity_docs')
                    ->image()
                    ->visibility('public')
                    ->nullable(),
                FileUpload::make('lisence_file')
                    ->label('Upload SIM')
                    ->disk('public')
                    ->directory('license_docs')
                    ->image()
                    ->visibility('public')
                    ->nullable(),
                Textarea::make('alamat')
                    ->label('Alamat')
                    ->rows(3)
                    ->columnSpanFull()
                    ->required(),
            ]),
        ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Informasi Pribadi')
                    ->schema([
                        Infolists\Components\TextEntry::make('nama'),
                        Infolists\Components\TextEntry::make('no_telp')->label('No. HP'),
                        Infolists\Components\TextEntry::make('ktp')->label('No. KTP'),
                        Infolists\Components\TextEntry::make('lisence')->label('No. SIM'),
                        Infolists\Components\TextEntry::make('alamat')->columnSpanFull(),
                    ])->columns(2),
                Infolists\Components\Section::make('Dokumen')
                    ->schema([
                        Infolists\Components\ImageEntry::make('identity_file')->label('Scan KTP'),
                        Infolists\Components\ImageEntry::make('lisence_file')->label('Scan SIM'),
                    ])->columns(2),
            ]);
    }

    public static function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->recordUrl(null)
            ->columns([
                TextColumn::make('nama')->label('Nama')->searchable()->wrap()
                    ->width(150),
                TextColumn::make('ktp')->label('No KTP')->wrap()
                    ->width(150),

                TextColumn::make('no_telp')->label('HP'),
                TextColumn::make('alamat')->label('Alamat')->limit(1000)->wrap()
                    ->width(150),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('')
                    ->tooltip('Edit Data Penyewa')
                    ->icon('heroicon-o-pencil')
                    ->color('warning')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\DeleteAction::make()
                    ->label('')
                    ->tooltip('Hapus Data Penyewa')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\ViewAction::make()
                    ->label('') // kosongkan label
                    ->tooltip('Detail Penyewa')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->hiddenLabel()
                    ->button(),
                Tables\Actions\Action::make('downloadKtp')
                    ->label('')
                    ->hiddenLabel()
                    ->tooltip('Download KTP')
                    ->icon('heroicon-o-identification')
                    ->color('success')
                    ->button()
                    ->url(fn($record) => route('customers.download.ktp', $record), true)
                    ->openUrlInNewTab(false)
                    ->hidden(fn($record) => !$record->identity_file),

                Tables\Actions\Action::make('downloadSim')
                    ->label('')
                    ->hiddenLabel()
                    ->button()
                    ->tooltip('Download SIM')
                    ->icon('heroicon-o-wallet')
                    ->color('primary')
                    ->url(fn($record) => route('customers.download.sim', $record), true)
                    ->openUrlInNewTab(false)
                    ->hidden(fn($record) => !$record->lisence_file),

            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\BookingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCustomers::route('/'),
            'create' => Pages\CreateCustomer::route('/create'),
            'view' => Pages\ViewCustomer::route('/{record}'),
            'edit' => Pages\EditCustomer::route('/{record}/edit'),
        ];
    }
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
