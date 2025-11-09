<?php

namespace App\Filament\Resources\Receptions\Pages;

use App\Filament\Resources\Receptions\ReceptionResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewReception extends ViewRecord
{
    protected static string $resource = ReceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
