<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Existing installs seeded the "Paeds M.O" shift services under OPD. Move
 * them to the new PED department (fresh installs get them there directly
 * from ServicesAndDepartmentsSeeder). Billing and provider settings are left
 * untouched; past service orders keep their OPD numbers.
 */
return new class extends Migration
{
    public function up(): void
    {
        $opdId = DB::table('service_departments')->where('slug', 'OPD')->value('id');

        $paedsServices = DB::table('services')
            ->where('service_department_id', $opdId)
            ->where('name', 'like', 'Paeds M.O%')
            ->pluck('id');

        if ($opdId === null || $paedsServices->isEmpty()) {
            return;
        }

        $pedId = DB::table('service_departments')->where('slug', 'PED')->value('id')
            ?? DB::table('service_departments')->insertGetId([
                'name' => 'Peds',
                'slug' => 'PED',
                'image' => '/img/ped.png',
                'have_composit_services' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

        DB::table('services')->whereIn('id', $paedsServices)->update([
            'service_department_id' => $pedId,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        $opdId = DB::table('service_departments')->where('slug', 'OPD')->value('id');
        $pedId = DB::table('service_departments')->where('slug', 'PED')->value('id');

        if ($opdId === null || $pedId === null) {
            return;
        }

        DB::table('services')
            ->where('service_department_id', $pedId)
            ->where('name', 'like', 'Paeds M.O%')
            ->update(['service_department_id' => $opdId, 'updated_at' => now()]);
    }
};
