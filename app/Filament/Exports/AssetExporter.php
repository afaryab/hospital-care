<?php

namespace App\Filament\Exports;

use App\Models\Asset;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class AssetExporter extends Exporter
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('asset_number'),
            ExportColumn::make('name'),
            ExportColumn::make('category.name')->label('Asset Category'),
            ExportColumn::make('serial_number'),
            ExportColumn::make('purchase_date'),
            ExportColumn::make('purchase_cost'),
            ExportColumn::make('vendor_name'),
            ExportColumn::make('warranty_expiry'),
            ExportColumn::make('location'),
            ExportColumn::make('status'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your asset export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
