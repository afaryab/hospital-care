<?php

namespace App\Filament\Resources\ServiceDepartments\Pages;

use App\Filament\Resources\ServiceDepartments\ServiceDepartmentResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceDepartment extends ViewRecord
{
    protected static string $resource = ServiceDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
