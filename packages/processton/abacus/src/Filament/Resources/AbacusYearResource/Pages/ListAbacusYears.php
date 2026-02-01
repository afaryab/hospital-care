<?php

namespace Processton\Abacus\Filament\Resources\AbacusYearResource\Pages;

use Processton\Abacus\Filament\Resources\AbacusYearResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
