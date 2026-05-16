<?php

namespace App\Filament\Admin\Resources\StockItems\Pages;

use App\Filament\Admin\Resources\StockItems\StockItemResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListStockItems extends ListRecords
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
