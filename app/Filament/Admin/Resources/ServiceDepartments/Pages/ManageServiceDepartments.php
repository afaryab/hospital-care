<?php

namespace App\Filament\Admin\Resources\ServiceDepartments\Pages;

use App\Filament\Admin\Resources\ServiceDepartments\ServiceDepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceDepartments extends ManageRecords
{
    protected static string $resource = ServiceDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
