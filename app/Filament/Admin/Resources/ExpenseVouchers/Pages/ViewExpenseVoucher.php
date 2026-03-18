<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Pages;

use App\Filament\Admin\Resources\ExpenseVouchers\ExpenseVoucherResource;
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
