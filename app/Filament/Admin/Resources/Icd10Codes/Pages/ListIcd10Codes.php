<?php

namespace App\Filament\Admin\Resources\Icd10Codes\Pages;

use App\Filament\Admin\Resources\Icd10Codes\Icd10CodeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListIcd10Codes extends ListRecords
{
    protected static string $resource = Icd10CodeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
