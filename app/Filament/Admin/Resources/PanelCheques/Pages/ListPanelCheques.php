<?php

namespace App\Filament\Admin\Resources\PanelCheques\Pages;

use App\Filament\Admin\Resources\PanelCheques\PanelChequeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPanelCheques extends ListRecords
{
    protected static string $resource = PanelChequeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
