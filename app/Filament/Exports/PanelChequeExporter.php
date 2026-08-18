<?php

namespace App\Filament\Exports;

use App\Models\PanelCheque;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class PanelChequeExporter extends Exporter
{
    protected static ?string $model = PanelCheque::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('panel.name')->label('Panel'),
            ExportColumn::make('bankAccount.account_number')->label('Bank Account Number'),
            ExportColumn::make('cheque_number'),
            ExportColumn::make('amount'),
            ExportColumn::make('due_date'),
            ExportColumn::make('status'),
            ExportColumn::make('received_at'),
            ExportColumn::make('notes'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your panel cheque export has completed and '.Number::format($export->successful_rows).' '.str('row')->plural($export->successful_rows).' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to export.';
        }

        return $body;
    }
}
