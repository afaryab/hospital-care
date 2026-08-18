<?php

namespace App\Filament\Admin\Resources\DmsClassifications\Pages;

use App\Filament\Admin\Resources\DmsClassifications\DmsClassificationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditDmsClassification extends EditRecord
{
    protected static string $resource = DmsClassificationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
