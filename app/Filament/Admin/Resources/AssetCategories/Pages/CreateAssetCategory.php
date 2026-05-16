<?php

namespace App\Filament\Admin\Resources\AssetCategories\Pages;

use App\Filament\Admin\Resources\AssetCategories\AssetCategoryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAssetCategory extends CreateRecord
{
    protected static string $resource = AssetCategoryResource::class;
}
