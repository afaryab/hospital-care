<?php

namespace App\Filament\Admin\Resources\Receaveables\Pages;

use App\Filament\Admin\Resources\Receaveables\ReceaveableResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditReceaveable extends EditRecord
{
    protected static string $resource = ReceaveableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
