<?php

namespace App\Filament\Admin\Resources\SalaryStructures\Pages;

use App\Filament\Admin\Resources\SalaryStructures\SalaryStructureResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSalaryStructure extends EditRecord
{
    protected static string $resource = SalaryStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
