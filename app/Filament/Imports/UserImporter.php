<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Imports core account fields only — name, username, email, mobile. Not
 * password (a random one is generated below; imported plaintext passwords
 * would be a real security risk) and not department/doctor profiles
 * (Administrator, OpdDoctor, etc.) — those still need assigning by hand via
 * the Users resource afterward, since one user can hold several.
 */
class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('username')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('mobile')
                ->rules(['nullable', 'max:50']),
        ];
    }

    public function resolveRecord(): User
    {
        return User::firstOrNew([
            'email' => $this->data['email'],
        ]);
    }

    protected function beforeSave(): void
    {
        if (! $this->record->exists) {
            $this->record->password = Str::password(16);
            $this->record->is_active = true;
        }
    }

    public function getValidationRules(): array
    {
        return [
            ...parent::getValidationRules(),
            'username' => ['required', 'max:255', Rule::unique('users', 'username')->ignore($this->record?->id)],
        ];
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and '.Number::format($import->successful_rows).' '.str('row')->plural($import->successful_rows).' imported. New accounts were given a random password — reset it for each before use, and assign department/doctor profiles from the Users list.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' '.str('row')->plural($failedRowsCount).' failed to import.';
        }

        return $body;
    }
}
