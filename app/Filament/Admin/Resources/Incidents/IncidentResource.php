<?php

namespace App\Filament\Admin\Resources\Incidents;

use App\Filament\Admin\Resources\Incidents\Pages\CreateIncident;
use App\Filament\Admin\Resources\Incidents\Pages\ListIncidents;
use App\Filament\Admin\Resources\Incidents\Pages\ViewIncident;
use App\Filament\Admin\Resources\Incidents\Schemas\IncidentForm;
use App\Filament\Admin\Resources\Incidents\Schemas\IncidentInfolist;
use App\Filament\Admin\Resources\Incidents\Tables\IncidentsTable;
use App\Models\Incident;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class IncidentResource extends Resource
{
    protected static ?string $model = Incident::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShieldExclamation;

    protected static string|UnitEnum|null $navigationGroup = 'Compliance';

    protected static ?string $navigationLabel = 'Incidents';

    protected static ?string $modelLabel = 'Incident';

    protected static ?string $pluralModelLabel = 'Incidents';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return IncidentForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return IncidentInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return IncidentsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListIncidents::route('/'),
            'create' => CreateIncident::route('/create'),
            'view' => ViewIncident::route('/{record}'),
        ];
    }
}
