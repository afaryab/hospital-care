<?php

namespace App\Filament\Accounts\Resources\ExpenseVouchers\Pages;

use App\Filament\Accounts\Resources\ExpenseVouchers\ExpenseVoucherResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExpenseVouchers extends ListRecords
{
    protected static string $resource = ExpenseVoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
