<?php

namespace App\Filament\Accounts\Resources\ExpenseVouchers\Pages;

use App\Filament\Accounts\Resources\ExpenseVouchers\ExpenseVoucherResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditExpenseVoucher extends EditRecord
{
    protected static string $resource = ExpenseVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
