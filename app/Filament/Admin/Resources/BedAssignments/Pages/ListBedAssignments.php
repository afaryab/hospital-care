<?php

namespace App\Filament\Admin\Resources\BedAssignments\Pages;

use App\Filament\Admin\Resources\BedAssignments\BedAssignmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBedAssignments extends ListRecords
{
    protected static string $resource = BedAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
