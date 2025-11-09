<?php

namespace App\Filament\Resources\ServiceRecestations\Pages;

use App\Filament\Resources\ServiceRecestations\ServiceRecestationResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditServiceRecestation extends EditRecord
{
    protected static string $resource = ServiceRecestationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
