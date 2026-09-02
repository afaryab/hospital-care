<?php

namespace App\Filament\Admin\Resources\Incidents\Pages;

use App\Enum\IncidentStatus;
use App\Filament\Admin\Resources\Incidents\IncidentResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateIncident extends CreateRecord
{
    protected static string $resource = IncidentResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['reported_by'] = Auth::id();
        $data['status'] = IncidentStatus::Reported;

        if (! empty($data['description'])) {
            $data['context'] = ['description' => $data['description']];
        }
        unset($data['description']);

        return $data;
    }
}
