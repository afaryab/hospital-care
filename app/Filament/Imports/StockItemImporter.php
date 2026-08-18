<?php

namespace App\Filament\Imports;

use App\Models\StockItem;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class StockItemImporter extends Importer
{
    protected static ?string $model = StockItem::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('sku')
                ->requiredMapping()
                ->rules(['required', 'max:100']),
            ImportColumn::make('category')
                ->label('Stock Category')
                ->requiredMapping()
                ->relationship(resolveUsing: 'name'),
            ImportColumn::make('unit')
                ->requiredMapping()
                ->rules(['required', 'max:50']),
            ImportColumn::make('reorder_level')
                ->integer(),
            ImportColumn::make('default_vendor')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('is_active')->boolean(),
        ];
    }

    public function resolveRecord(): StockItem
    {
        return StockItem::firstOrNew([
            'sku' => $this->data['sku'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your stock item import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
