<?php

namespace App\Filament\Resources\ServiceRecestations\Pages;

use App\Filament\Resources\ServiceRecestations\ServiceRecestationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewServiceRecestation extends ViewRecord
{
    protected static string $resource = ServiceRecestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
