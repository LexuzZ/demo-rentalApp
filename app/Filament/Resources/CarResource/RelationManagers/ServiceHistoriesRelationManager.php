<?php

namespace App\Filament\Resources\CarResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ServiceHistoriesRelationManager extends RelationManager
{
    protected static string $relationship = 'serviceHistories';
    protected static ?string $title = 'Riwayat Service';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\DatePicker::make('service_date')
                    ->label('Tanggal Service')
                    ->required(),
                Forms\Components\TextInput::make('current_km')
                    ->label('KM Saat Ini')
                    ->numeric()
                    ->required(),
                Forms\Components\Textarea::make('description')
                    ->label('Deskripsi')
                    ->required()
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('workshop')
                    ->label('Nama Bengkel'),
                Forms\Components\TextInput::make('next_km')
                    ->label('KM Service Berikutnya'),
                Forms\Components\DatePicker::make('next_service_date')
                    ->label('Tanggal Service Berikutnya'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('service_date')
            ->columns([
                Tables\Columns\TextColumn::make('service_date')
                    ->label('Tgl. Service')
                    ->date('d M Y')
                    ,
                Tables\Columns\TextColumn::make('jenis_service')
                    ->label('Jenis Service')
                    ->badge()->alignCenter()
                    ->colors(['success' => 'service', 'info' => 'ganti_aki', 'primary' => 'ganti_ban'])
                    ->formatStateUsing(fn($state) => match ($state) {
                        'service' => 'Service & Tune Up',
                        'ganti_aki' => 'Pergantian Aki',
                        'ganti_ban' => 'Pergantian Ban',
                        default => ucfirst($state)
                    })
                    ->wrap()
                    ->width(150)
                    ->searchable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(1000)
                    ->wrap()
                    ->width(550),
                Tables\Columns\TextColumn::make('next_km')
                    ->label('Next Km')
                    ->numeric(),
                Tables\Columns\TextColumn::make('workshop')
                    ->searchable()
                    ->wrap()
                    ->width(150),
                Tables\Columns\TextColumn::make('next_service_date')
                    ->label('Jadwal Berikutnya')
                    ->date('d M Y')

                    ->wrap()
                    ->width(150),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('service_date', 'desc');
    }
}
