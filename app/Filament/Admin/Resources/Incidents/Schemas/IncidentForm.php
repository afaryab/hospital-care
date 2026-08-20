<?php

namespace App\Filament\Admin\Resources\Incidents\Schemas;

use App\Enum\IncidentSeverity;
use App\Enum\IncidentType;
use App\Models\Patient;
use App\Models\ServiceDepartment;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class IncidentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('type')
                ->label('Incident Type')
                ->options(collect(IncidentType::manuallyReportable())->mapWithKeys(fn (IncidentType $t) => [$t->value => $t->label()]))
                ->required(),
            Select::make('severity')
                ->label('Initial Severity')
                ->helperText('Can be revised during classification.')
                ->options(collect(IncidentSeverity::cases())->mapWithKeys(fn (IncidentSeverity $s) => [$s->value => $s->label()]))
                ->default(IncidentSeverity::Medium->value)
                ->required(),
            Select::make('department_id')
                ->label('Department (Optional)')
                ->options(fn (): array => ServiceDepartment::cachedAll()->pluck('name', 'id')->toArray())
                ->searchable()
                ->nullable(),
            Select::make('patient_id')
                ->label('Patient (Optional)')
                ->searchable()
                ->nullable()
                ->getSearchResultsUsing(fn (string $search): array => Patient::query()
                    ->where('name', 'like', "%{$search}%")
                    ->limit(30)
                    ->pluck('name', 'id')
                    ->toArray())
                ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name),
            DateTimePicker::make('occurred_at')
                ->label('Occurred At')
                ->required()
                ->default(now()),
            Textarea::make('description')
                ->label('Description')
                ->required()
                ->columnSpanFull(),
        ]);
    }
}
