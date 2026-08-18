<?php

namespace App\Filament\Exports;

use App\Models\Patient;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PatientExporter extends Exporter
{
    protected static ?string $model = Patient::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('ps_number'),
            ExportColumn::make('name'),
            ExportColumn::make('gender'),
            ExportColumn::make('contact'),
            ExportColumn::make('cnic'),
            ExportColumn::make('address'),
            ExportColumn::make('guardian'),
            ExportColumn::make('relation'),
            ExportColumn::make('age_dob')->label('Date of Birth'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your patient export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
