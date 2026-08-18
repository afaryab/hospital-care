<?php

namespace App\Filament\Imports;

use App\Models\ServiceDepartment;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;

class ServiceDepartmentImporter extends Importer
{
    protected static ?string $model = ServiceDepartment::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('slug')
                ->rules(['nullable', 'max:255']),
        ];
    }

    public function resolveRecord(): ServiceDepartment
    {
        $record = ServiceDepartment::firstOrNew([
            'name' => $this->data['name'],
        ]);

        if (! $record->exists) {
            if (empty($this->data['slug'] ?? null)) {
                $record->slug = Str::slug($this->data['name']);
            }

            // image and have_composit_services have no database default —
            // every new row must set them explicitly. The image can be
            // uploaded afterward from the Service Departments resource.
            $record->image = '';
            $record->have_composit_services = false;
        }

        return $record;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your service department import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
