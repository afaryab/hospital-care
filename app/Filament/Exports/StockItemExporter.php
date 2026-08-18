<?php

namespace App\Filament\Exports;

use App\Models\StockItem;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class StockItemExporter extends Exporter
{
    protected static ?string $model = StockItem::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('sku'),
            ExportColumn::make('category.name')->label('Stock Category'),
            ExportColumn::make('unit'),
            ExportColumn::make('reorder_level'),
            ExportColumn::make('default_vendor'),
            ExportColumn::make('is_active'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your stock item export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
