<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Pages;

use App\Filament\Admin\Resources\ExpenseVouchers\ExpenseVoucherResource;
use Filament\Resources\Pages\CreateRecord;

class CreateExpenseVoucher extends CreateRecord
{
    protected static string $resource = ExpenseVoucherResource::class;
}
