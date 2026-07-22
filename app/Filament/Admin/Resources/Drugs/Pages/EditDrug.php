<?php

namespace App\Filament\Admin\Resources\Drugs\Pages;

use App\Filament\Admin\Resources\Drugs\DrugResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDrug extends EditRecord
{
    protected static string $resource = DrugResource::class;

    protected function getHeaderActions(): array
    {
        return [DeleteAction::make()];
    }
}
