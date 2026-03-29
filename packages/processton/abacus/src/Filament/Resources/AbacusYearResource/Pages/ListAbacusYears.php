<?php

namespace Processton\Abacus\Filament\Resources\AbacusYearResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Processton\Abacus\Filament\Resources\AbacusYearResource;

class ListAbacusYears extends ListRecords
{
    protected static string $resource = AbacusYearResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modal(),
        ];
    }
}
