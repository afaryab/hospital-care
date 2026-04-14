<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Pages;

use App\Filament\Admin\Resources\AdministrativeTransactions\AdministrativeTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAdministrativeTransaction extends ViewRecord
{
    protected static string $resource = AdministrativeTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
