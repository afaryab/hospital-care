<?php

namespace App\Filament\Imports;

use App\Models\BankAccount;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class BankAccountImporter extends Importer
{
    protected static ?string $model = BankAccount::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('bank_name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('account_number')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('branch_code')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('iban')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('is_active')->boolean(),
        ];
    }

    public function resolveRecord(): BankAccount
    {
        return BankAccount::firstOrNew([
            'account_number' => $this->data['account_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your bank account import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
