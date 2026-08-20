<?php

namespace App\Filament\Admin\Resources\Consents\Pages;

use App\Filament\Admin\Resources\Consents\ConsentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListConsents extends ListRecords
{
    protected static string $resource = ConsentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
