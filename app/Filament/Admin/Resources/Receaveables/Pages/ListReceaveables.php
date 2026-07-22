<?php

namespace App\Filament\Admin\Resources\Receaveables\Pages;

use App\Filament\Admin\Resources\Receaveables\ReceaveableResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReceaveables extends ListRecords
{
    protected static string $resource = ReceaveableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
