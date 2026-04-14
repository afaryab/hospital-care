<?php

namespace App\Filament\Admin\Resources\BankTransactions\Pages;

use App\Filament\Admin\Resources\BankTransactions\BankTransactionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBankTransaction extends CreateRecord
{
    protected static string $resource = BankTransactionResource::class;
}
