<?php

namespace Processton\Abacus\Filament\Resources\AbacusTransactionResource\Pages;

use Processton\Abacus\Filament\Resources\AbacusTransactionResource;
use BackedEnum;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAbacusTransactions extends ListRecords
{
    protected static string $resource = AbacusTransactionResource::class;

    protected static ?string $title = 'Enteries';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-table-cells';

    protected static ?string $breadcrumb = 'Records';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make()->modal()->label('Add Transaction')->icon('heroicon-o-plus-circle')->iconPosition('after'),
        ];
    }
}
