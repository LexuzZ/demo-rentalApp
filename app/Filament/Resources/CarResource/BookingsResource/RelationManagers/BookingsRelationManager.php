<?php

namespace App\Filament\Resources\CarResource\BookingsResource\RelationManagers;

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
    protected static ?string $title = 'Riwayat Penyewaan';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.nama')->label('Pelanggan'),
                Tables\Columns\TextColumn::make('tanggal_keluar')->date(),
                Tables\Columns\TextColumn::make('tanggal_kembali')->date(),
                Tables\Columns\TextColumn::make('status')->badge(),
            ]);
    }
}
