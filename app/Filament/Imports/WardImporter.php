<?php

namespace App\Filament\Imports;

use App\Models\Ward;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class WardImporter extends Importer
{
    protected static ?string $model = Ward::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('type')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('floor')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('building')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('notes')
                ->rules(['nullable']),
            ImportColumn::make('is_active')->boolean(),
        ];
    }

    public function resolveRecord(): Ward
    {
        return Ward::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your ward import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
