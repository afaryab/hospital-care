<?php

namespace Database\Seeders;

use App\Enum\DepreciationMethod;
use App\Models\AssetCategory;
use Illuminate\Database\Seeder;

class AssetCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Medical Equipment', 'depreciation_method' => DepreciationMethod::StraightLine->value, 'useful_life_years' => 10],
            ['name' => 'Furniture', 'depreciation_method' => DepreciationMethod::StraightLine->value, 'useful_life_years' => 7],
            ['name' => 'IT Equipment', 'depreciation_method' => DepreciationMethod::DecliningBalance->value, 'useful_life_years' => 3],
            ['name' => 'Vehicles', 'depreciation_method' => DepreciationMethod::StraightLine->value, 'useful_life_years' => 5],
            ['name' => 'Other', 'depreciation_method' => DepreciationMethod::None->value, 'useful_life_years' => null],
        ];

        foreach ($categories as $category) {
            AssetCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
