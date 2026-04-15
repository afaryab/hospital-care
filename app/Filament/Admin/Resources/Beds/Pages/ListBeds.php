<?php

namespace App\Filament\Admin\Resources\Beds\Pages;

use App\Filament\Admin\Resources\Beds\BedResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBeds extends ListRecords
{
    protected static string $resource = BedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
