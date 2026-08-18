<?php

namespace App\Filament\Imports;

use App\Models\PanelCheque;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class PanelChequeImporter extends Importer
{
    protected static ?string $model = PanelCheque::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('panel')
                ->label('Panel')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('bankAccount')
                ->label('Bank Account Number')
                ->relationship(resolveUsing: 'account_number'),
            ImportColumn::make('cheque_number')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('amount')
                ->requiredMapping()
                ->numeric(decimalPlaces: 2)
                ->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('due_date')
                ->rules(['nullable', 'date']),
            ImportColumn::make('status')
                ->rules(['nullable', 'in:pending,received,bounced']),
            ImportColumn::make('received_at')
                ->rules(['nullable', 'date']),
            ImportColumn::make('notes')
                ->rules(['nullable']),
        ];
    }

    public function resolveRecord(): PanelCheque
    {
        return PanelCheque::firstOrNew([
            'cheque_number' => $this->data['cheque_number'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your panel cheque import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
