<?php

namespace App\Filament\Admin\Resources\DmsClassifications\Pages;

use App\Filament\Admin\Resources\DmsClassifications\DmsClassificationResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListDmsClassifications extends ListRecords
{
    protected static string $resource = DmsClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
