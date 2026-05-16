<?php

namespace App\Filament\Admin\Resources\StockCategories\Pages;

use App\Filament\Admin\Resources\StockCategories\StockCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockCategory extends CreateRecord
{
    protected static string $resource = StockCategoryResource::class;
}
