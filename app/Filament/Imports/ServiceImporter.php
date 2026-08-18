<?php

namespace App\Filament\Imports;

use App\Models\Service;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ServiceImporter extends Importer
{
    protected static ?string $model = Service::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('department')
                ->label('Service Department')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('charges')
                ->requiredMapping()
                ->numeric(decimalPlaces: 2)
                ->rules(['required', 'numeric', 'min:0']),
            ImportColumn::make('tax_rate')
                ->numeric(decimalPlaces: 2),
            ImportColumn::make('charges_include_tax')->boolean(),
            ImportColumn::make('is_featured')->boolean(),
        ];
    }

    public function resolveRecord(): Service
    {
        $record = Service::firstOrNew([
            'name' => $this->data['name'],
        ]);

        if (! $record->exists) {
            if (empty($this->data['slug'] ?? null)) {
                $record->slug = Str::slug($this->data['name']);
            }

            // charges_include_tax, tax_rate, and created_by have no database
            // default (unlike the model's other boolean flags) — every new
            // row must set them explicitly.
            if (! array_key_exists('charges_include_tax', $this->columnMap)) {
                $record->charges_include_tax = false;
            }

            if (! array_key_exists('tax_rate', $this->columnMap)) {
                $record->tax_rate = 0;
            }

            $record->created_by = auth()->id();
        }

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your service import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
