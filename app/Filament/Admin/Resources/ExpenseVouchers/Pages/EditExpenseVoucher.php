<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Pages;

use App\Filament\Admin\Resources\ExpenseVouchers\ExpenseVoucherResource;
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
