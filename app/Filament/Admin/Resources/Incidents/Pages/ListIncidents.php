<?php

namespace App\Filament\Admin\Resources\Incidents\Pages;

use App\Filament\Admin\Resources\Incidents\IncidentResource;
use Filament\Resources\Pages\ListRecords;

class ListIncidents extends ListRecords
{
    protected static string $resource = IncidentResource::class;
}
