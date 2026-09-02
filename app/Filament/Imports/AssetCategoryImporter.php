<?php

namespace App\Filament\Imports;

use App\Enum\DepreciationMethod;
use App\Models\AssetCategory;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Validation\Rules\Enum;

class AssetCategoryImporter extends Importer
{
    protected static ?string $model = AssetCategory::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('depreciation_method')
                ->rules(['nullable', new Enum(DepreciationMethod::class)]),
            ImportColumn::make('useful_life_years')
                ->integer(),
        ];
    }

    public function resolveRecord(): AssetCategory
    {
        return AssetCategory::firstOrNew([
            'name' => $this->data['name'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your asset category import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
