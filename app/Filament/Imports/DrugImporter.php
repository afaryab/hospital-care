<?php

namespace App\Filament\Imports;

use App\Models\Drug;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class DrugImporter extends Importer
{
    protected static ?string $model = Drug::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('generic_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('type')
                ->rules(['nullable', 'in:'.implode(',', Drug::types())]),
            ImportColumn::make('category')
                ->label('Drug Category')
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('strength')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('manufacturer')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('default_dose')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('default_frequency')
                ->rules(['nullable', 'in:'.implode(',', Drug::frequencies())]),
            ImportColumn::make('default_duration')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('default_route')
                ->rules(['nullable', 'in:'.implode(',', Drug::routes())]),
            ImportColumn::make('usage_instructions')
                ->rules(['nullable']),
            ImportColumn::make('contraindications')
                ->rules(['nullable']),
            ImportColumn::make('side_effects')
                ->rules(['nullable']),
            ImportColumn::make('is_active')->boolean(),
        ];
    }

    public function resolveRecord(): Drug
    {
        return Drug::firstOrNew([
            'name' => $this->data['name'],
            'generic_name' => $this->data['generic_name'] ?? null,
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your drug import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
