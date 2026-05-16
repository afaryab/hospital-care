<?php

namespace App\Filament\Admin\Resources\SalaryStructures\Pages;

use App\Filament\Admin\Resources\SalaryStructures\SalaryStructureResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSalaryStructures extends ListRecords
{
    protected static string $resource = SalaryStructureResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
