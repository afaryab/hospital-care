<?php

namespace App\Filament\Imports;

use App\Models\ExpenseCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class ExpenseCategoryImporter extends Importer
{
    protected static ?string $model = ExpenseCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('pay_doc')->boolean(),
            ImportColumn::make('pay_others')->boolean(),
            ImportColumn::make('pay_users')->boolean(),
            ImportColumn::make('pay_patient')->boolean(),
            ImportColumn::make('allow_petty_cash')->boolean(),
            ImportColumn::make('allow_voucher')->boolean(),
        ];
    }

    public function resolveRecord(): ExpenseCategory
    {
        return ExpenseCategory::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your expense category import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
