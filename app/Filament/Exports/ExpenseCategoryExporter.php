<?php

namespace App\Filament\Exports;

use App\Models\ExpenseCategory;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ExpenseCategoryExporter extends Exporter
{
    protected static ?string $model = ExpenseCategory::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name'),
            ExportColumn::make('type'),
            ExportColumn::make('pay_doc'),
            ExportColumn::make('pay_others'),
            ExportColumn::make('pay_users'),
            ExportColumn::make('pay_patient'),
            ExportColumn::make('allow_petty_cash'),
            ExportColumn::make('allow_voucher'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your expense category export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
