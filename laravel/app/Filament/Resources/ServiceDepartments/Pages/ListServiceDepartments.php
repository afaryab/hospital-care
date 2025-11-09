<?php

namespace App\Filament\Resources\ServiceDepartments\Pages;

use App\Filament\Resources\ServiceDepartments\ServiceDepartmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceDepartments extends ListRecords
{
    protected static string $resource = ServiceDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
