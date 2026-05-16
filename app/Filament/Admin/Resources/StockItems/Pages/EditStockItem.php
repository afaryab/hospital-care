<?php

namespace App\Filament\Admin\Resources\StockItems\Pages;

use App\Filament\Admin\Resources\StockItems\StockItemResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditStockItem extends EditRecord
{
    protected static string $resource = StockItemResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
