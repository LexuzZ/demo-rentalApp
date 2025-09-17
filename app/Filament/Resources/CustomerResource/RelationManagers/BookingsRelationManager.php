<?php

namespace App\Filament\Resources\CustomerResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookingsRelationManager extends RelationManager
{
    protected static string $relationship = 'bookings';
    protected static ?string $title = 'Riwayat Pemesanan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                // Form tidak diperlukan di sini karena ini hanya untuk tampilan
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('id')->label('Booking ID'),
                Tables\Columns\TextColumn::make('car.carModel.name')
                    ->label('Mobil'),
                Tables\Columns\TextColumn::make('car.nopol')
                    ->label('No. Polisi'),
                Tables\Columns\TextColumn::make('tanggal_keluar')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('tanggal_kembali')
                    ->date('d M Y'),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->colors([
                        'success' => 'disewa',
                        'info' => 'booking',
                        'gray' => 'selesai',
                        'danger' => 'batal',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'disewa' => 'Disewa',
                        'booking' => 'Booking',
                        'selesai' => 'Selesai',
                        'batal' => 'Batal',
                        default => ucfirst($state),
                    }),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tombol "Create" tidak diperlukan di sini
            ])
            ->actions([
                 Tables\Actions\Action::make('viewBooking')
                    ->label('Lihat Detail')
                    ->url(fn ($record) => \App\Filament\Resources\BookingResource::getUrl('view', ['record' => $record]))
                    ->icon('heroicon-o-eye'),
            ])
            ->bulkActions([]);
    }
}
