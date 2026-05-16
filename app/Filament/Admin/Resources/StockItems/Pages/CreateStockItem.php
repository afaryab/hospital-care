<?php

namespace App\Filament\Admin\Resources\StockItems\Pages;

use App\Filament\Admin\Resources\StockItems\StockItemResource;
use Filament\Resources\Pages\CreateRecord;

class CreateStockItem extends CreateRecord
{
    protected static string $resource = StockItemResource::class;
}
