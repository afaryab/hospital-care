<?php

namespace App\Filament\Admin\Resources\BankTransactions\Pages;

use App\Filament\Admin\Resources\BankTransactions\BankTransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBankTransaction extends EditRecord
{
    protected static string $resource = BankTransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
