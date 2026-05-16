<?php

namespace App\Filament\Admin\Resources\Icd10Codes\Pages;

use App\Filament\Admin\Resources\Icd10Codes\Icd10CodeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditIcd10Code extends EditRecord
{
    protected static string $resource = Icd10CodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
