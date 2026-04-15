<?php

namespace App\Filament\Admin\Resources\BedAssignments\Pages;

use App\Filament\Admin\Resources\BedAssignments\BedAssignmentResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;

class EditBedAssignment extends EditRecord
{
    protected static string $resource = BedAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
