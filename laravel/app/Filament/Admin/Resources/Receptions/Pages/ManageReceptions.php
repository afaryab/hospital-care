<?php

namespace App\Filament\Admin\Resources\Receptions\Pages;

use App\Filament\Admin\Resources\Receptions\ReceptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageReceptions extends ManageRecords
{
    protected static string $resource = ReceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
