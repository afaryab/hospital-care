<?php

namespace App\Filament\Imports;

use App\Enum\AssetStatus;
use App\Models\Asset;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\Rules\Enum;

class AssetImporter extends Importer
{
    protected static ?string $model = Asset::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('category')
                ->label('Asset Category')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('serial_number')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('purchase_date')
                ->rules(['nullable', 'date']),
            ImportColumn::make('purchase_cost')
                ->numeric(decimalPlaces: 2),
            ImportColumn::make('vendor_name')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('warranty_expiry')
                ->rules(['nullable', 'date']),
            ImportColumn::make('location')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('status')
                ->rules(['nullable', new Enum(AssetStatus::class)]),
        ];
    }

    public function resolveRecord(): Asset
    {
        if (! empty($this->data['serial_number'] ?? null)) {
            return Asset::firstOrNew([
                'serial_number' => $this->data['serial_number'],
            ]);
        }

        return new Asset;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your asset import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
