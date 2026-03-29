<?php

namespace Database\Seeders;

use App\Models\StockCategory;
use Illuminate\Database\Seeder;

class StockCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Medicines', 'is_medicine' => true],
            ['name' => 'Surgical Supplies', 'is_medicine' => false],
            ['name' => 'Stationery', 'is_medicine' => false],
            ['name' => 'Cleaning Supplies', 'is_medicine' => false],
            ['name' => 'General Supplies', 'is_medicine' => false],
        ];

        foreach ($categories as $category) {
            StockCategory::firstOrCreate(['name' => $category['name']], $category);
        }
    }
}
