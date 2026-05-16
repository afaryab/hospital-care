<?php

namespace App\Filament\Admin\Resources\AssetCategories\Pages;

use App\Filament\Admin\Resources\AssetCategories\AssetCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditAssetCategory extends EditRecord
{
    protected static string $resource = AssetCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
