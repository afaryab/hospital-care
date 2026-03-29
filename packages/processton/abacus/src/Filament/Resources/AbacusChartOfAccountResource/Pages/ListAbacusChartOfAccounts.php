<?php

namespace Processton\Abacus\Filament\Resources\AbacusChartOfAccountResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Processton\Abacus\Filament\Resources\AbacusChartOfAccountResource;

class ListAbacusChartOfAccounts extends ListRecords
{
    protected static string $resource = AbacusChartOfAccountResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modal(),
        ];
    }
}
