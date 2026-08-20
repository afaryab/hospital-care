<?php

namespace App\Filament\Admin\Resources\Patients\Pages;

use App\Filament\Admin\Resources\Patients\PatientResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPatient extends ViewRecord
{
    protected static string $resource = PatientResource::class;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        // The WebController::patient() front-desk view already logs
        // "viewed" — the Filament admin panel is a second, separate PHI
        // read path that needs the same audit trail.
        activity()
            ->causedBy(auth()->user())
            ->performedOn($this->record)
            ->event('viewed')
            ->log('Patient record viewed (admin panel)');
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
