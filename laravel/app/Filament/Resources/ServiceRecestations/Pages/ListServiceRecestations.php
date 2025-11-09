<?php

namespace App\Filament\Resources\ServiceRecestations\Pages;

use App\Filament\Resources\ServiceRecestations\ServiceRecestationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListServiceRecestations extends ListRecords
{
    protected static string $resource = ServiceRecestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
