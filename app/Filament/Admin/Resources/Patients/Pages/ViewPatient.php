<?php

namespace App\Filament\Admin\Resources\Patients\Pages;

use App\Filament\Admin\Resources\Patients\PatientResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;
}
