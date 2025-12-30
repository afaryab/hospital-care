<?php

namespace App\Filament\Accounts\Resources\Closings\Pages;

use App\Filament\Accounts\Resources\Closings\ClosingResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClosings extends ListRecords
{
    protected static string $resource = ClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
