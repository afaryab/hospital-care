<?php

namespace App\Filament\Exports;

use App\Models\ServiceDepartment;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ServiceDepartmentExporter extends Exporter
{
    protected static ?string $model = ServiceDepartment::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('slug'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your service department export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
