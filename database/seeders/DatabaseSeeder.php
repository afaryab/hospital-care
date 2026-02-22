<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Processton\Abacus\Models\AbacusChartOfAccount;
use Processton\Abacus\Models\AbacusYear;
use Processton\AbacusDatabase\Seeders\AbacusYearSeeder;
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
            ChartOfAccountsSeeder::class
        ]);

    }
}
