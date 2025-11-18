<?php

namespace App\Filament\Accounts\Resources\Closings\Pages;

use App\Filament\Accounts\Resources\Closings\ClosingResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewClosing extends ViewRecord
{
    protected static string $resource = ClosingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
