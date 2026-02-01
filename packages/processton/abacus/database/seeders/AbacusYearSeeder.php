<?php

namespace Processton\AbacusDatabase\Seeders;

use Processton\Abacus\Models\AbacusYear;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Processton\Abacus\Models\AbacusIncoming;
use Processton\Abacus\Models\AbacusTransaction;

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
