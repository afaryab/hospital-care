<?php

namespace App\Filament\Resources\ServiceDepartments\Pages;

use App\Filament\Resources\ServiceDepartments\ServiceDepartmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceDepartment extends EditRecord
{
    protected static string $resource = ServiceDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
