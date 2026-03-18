<?php

namespace App\Filament\Admin\Resources\Closings\Pages;

use App\Filament\Admin\Resources\Closings\ClosingResource;
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
