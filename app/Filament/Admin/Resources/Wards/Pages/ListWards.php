<?php

namespace App\Filament\Admin\Resources\Wards\Pages;

use App\Filament\Admin\Resources\Wards\WardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListWards extends ListRecords
{
    protected static string $resource = WardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
