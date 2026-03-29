<?php

namespace App\Filament\Admin\Resources\Patients\Pages;

use App\Filament\Admin\Resources\Patients\PatientResource;
use Filament\Resources\Pages\ListRecords;

class ListPatients extends ListRecords
{
    protected static string $resource = PatientResource::class;
}
