<?php

namespace App\Filament\Resources\MonthlyReportResource\Pages;

use App\Filament\Resources\MonthlyReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Model;

class ListMonthlyReports extends ListRecords
{
    protected static string $resource = MonthlyReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    public function getTableRecordKey(Model $record): string
    {
        // Kita gabungkan tahun dan bulan untuk membuat kunci yang unik, cth: "2025-8"
        return $record->year . '-' . $record->month;
    }
}
