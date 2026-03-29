<?php

namespace Processton\AbacusDatabase\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Processton\Abacus\Models\AbacusYear;

class AbacusYearSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            $year = AbacusYear::create([
                'start_date' => Carbon::create(2026, 1, 1),
                'end_date' => Carbon::create(2026, 12, 31),
            ]);

        });
    }
}
