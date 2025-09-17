<?php

namespace App\Filament\Resources\MonthlyReportResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class PaymentsRelationManager extends RelationManager
{
    protected static string $relationship = 'payments';
    protected static ?string $title = 'Detail Transaksi';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([
                Tables\Columns\TextColumn::make('invoice.id')->label('Faktur'),
                Tables\Columns\TextColumn::make('invoice.booking.customer.nama')->label('Pelanggan')->searchable(),
                Tables\Columns\TextColumn::make('invoice.booking.car.nopol')->label('No. Polisi'),
                Tables\Columns\TextColumn::make('tanggal_pembayaran')->label('Tanggal')->date('d M Y'),
                Tables\Columns\TextColumn::make('pembayaran')->label('Jumlah')->money('IDR', 0),
                Tables\Columns\TextColumn::make('status')->badge()->colors(['success' => 'lunas', 'danger' => 'belum_lunas']),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tombol create tidak diperlukan di sini
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                //
            ]);
    }
}
