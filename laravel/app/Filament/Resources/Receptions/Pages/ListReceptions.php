<?php

namespace App\Filament\Resources\Receptions\Pages;

use App\Filament\Resources\Receptions\ReceptionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReceptions extends ListRecords
{
    protected static string $resource = ReceptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
