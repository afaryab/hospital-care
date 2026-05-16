<?php

namespace App\Filament\Admin\Resources\StockCategories\Pages;

use App\Filament\Admin\Resources\StockCategories\StockCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockCategories extends ListRecords
{
    protected static string $resource = StockCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
