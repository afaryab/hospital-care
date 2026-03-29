<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Processton\AbacusDatabase\Seeders\ChartOfAccountsSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            ServicesAndDepartmentsSeeder::class,
            ExpenseCategorySeeder::class,
            ChartOfAccountsSeeder::class,
        ]);

    }
}
