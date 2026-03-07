<?php

namespace App\Filament\Accounts\Resources\ExpenseVouchers\Pages;

use App\Filament\Accounts\Resources\ExpenseVouchers\ExpenseVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseVoucher extends CreateRecord
{
    protected static string $resource = ExpenseVoucherResource::class;
}
