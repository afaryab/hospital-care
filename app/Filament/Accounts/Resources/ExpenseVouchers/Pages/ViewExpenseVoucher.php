<?php

namespace App\Filament\Accounts\Resources\ExpenseVouchers\Pages;

use App\Filament\Accounts\Resources\ExpenseVouchers\ExpenseVoucherResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewExpenseVoucher extends ViewRecord
{
    protected static string $resource = ExpenseVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
