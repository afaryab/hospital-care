<?php

namespace App\Filament\Admin\Resources\StockCategories\Pages;

use App\Filament\Admin\Resources\StockCategories\StockCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockCategory extends EditRecord
{
    protected static string $resource = StockCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
