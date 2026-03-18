<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Pages;

use App\Filament\Admin\Resources\ExpenseVouchers\ExpenseVoucherResource;
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
