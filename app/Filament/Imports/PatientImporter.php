<?php

namespace App\Filament\Imports;

use App\Helpers\PiiHasher;
use App\Models\Patient;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

/**
 * cnic/contact/address are encrypted at rest (SafeEncrypted cast) and CNIC
 * is looked up via its blind-index hash, not the encrypted column itself —
 * PatientObserver::creating() computes both hashes and the ps_number the
 * same way regardless of how the record was created, so importing through
 * Patient::create()/save() (what Filament's importer does) is safe.
 */
class PatientImporter extends Importer
{
    protected static ?string $model = Patient::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('gender')
                ->rules(['nullable', 'in:m,f,t,o']),
            ImportColumn::make('contact')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('cnic')
                ->rules(['nullable', 'max:50']),
            ImportColumn::make('address')
                ->rules(['nullable']),
            ImportColumn::make('guardian')
                ->rules(['nullable', 'max:255']),
            ImportColumn::make('relation')
                ->rules(['nullable', 'max:100']),
            ImportColumn::make('age_dob')
                ->label('Date of Birth')
                ->rules(['nullable', 'date']),
        ];
    }

    public function resolveRecord(): Patient
    {
        $cnic = trim((string) ($this->data['cnic'] ?? ''));

        if ($cnic !== '') {
            $existing = Patient::where('cnic_hash', PiiHasher::cnic($cnic))->first();

            if ($existing) {
                return $existing;
            }
        }

        return new Patient;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your patient import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
