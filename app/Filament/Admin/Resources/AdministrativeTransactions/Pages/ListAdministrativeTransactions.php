<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Pages;

use App\Filament\Admin\Resources\AdministrativeTransactions\AdministrativeTransactionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdministrativeTransactions extends ListRecords
{
    protected static string $resource = AdministrativeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
