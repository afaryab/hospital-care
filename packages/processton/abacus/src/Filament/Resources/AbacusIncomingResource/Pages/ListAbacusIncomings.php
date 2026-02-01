<?php

namespace Processton\Abacus\Filament\Resources\AbacusIncomingResource\Pages;

use Processton\Abacus\Filament\Resources\AbacusIncomingResource;
use BackedEnum;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Resources\Pages\ListRecords;

class ListAbacusIncomings extends ListRecords
{
    protected static string $resource = AbacusIncomingResource::class;

    protected static ?string $title = 'Transactions';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-inbox-arrow-down';

    protected static ?string $breadcrumb = 'Transactions';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->modal(),
        ];
    }
}
