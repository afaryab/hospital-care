<?php

namespace App\Filament\Exports;

use App\Models\Drug;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class DrugExporter extends Exporter
{
    protected static ?string $model = Drug::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('generic_name'),
            ExportColumn::make('type'),
            ExportColumn::make('category.name')->label('Drug Category'),
            ExportColumn::make('strength'),
            ExportColumn::make('manufacturer'),
            ExportColumn::make('default_dose'),
            ExportColumn::make('default_frequency'),
            ExportColumn::make('default_duration'),
            ExportColumn::make('default_route'),
            ExportColumn::make('is_active'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your drug export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
