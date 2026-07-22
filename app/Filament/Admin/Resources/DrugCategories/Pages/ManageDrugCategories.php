<?php

namespace App\Filament\Admin\Resources\DrugCategories\Pages;

use App\Filament\Admin\Resources\DrugCategories\DrugCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageDrugCategories extends ManageRecords
{
    protected static string $resource = DrugCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
