<?php

namespace App\Filament\Admin\Resources\ServiceRecestations\Pages;

use App\Filament\Admin\Resources\ServiceRecestations\ServiceRecestationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageServiceRecestations extends ManageRecords
{
    protected static string $resource = ServiceRecestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
