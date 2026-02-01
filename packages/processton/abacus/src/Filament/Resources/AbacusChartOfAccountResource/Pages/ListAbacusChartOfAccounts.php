<?php

namespace Processton\Abacus\Filament\Resources\AbacusChartOfAccountResource\Pages;

use Processton\Abacus\Filament\Resources\AbacusChartOfAccountResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

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
