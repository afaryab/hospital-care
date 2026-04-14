<?php

namespace App\Filament\Admin\Resources\PanelCheques\Pages;

use App\Filament\Admin\Resources\PanelCheques\PanelChequeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPanelCheque extends EditRecord
{
    protected static string $resource = PanelChequeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
