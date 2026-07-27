<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    private const DOCTOR_TABLES = [
        'opd_doctors',
        'ind_doctors',
        'emergency_doctors',
        'dentists',
        'ultrasound_doctors',
    ];

    public function up(): void
    {
        // PMDC (Pakistan Medical & Dental Council) registration number —
        // only doctor/dentist profiles need one, not other staff (nurses,
        // technicians, receptionists, etc.). Nullable at the DB level since
        // existing profiles predate this field; required going forward via
        // the Filament form instead.
        foreach (self::DOCTOR_TABLES as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->string('pmdc_number')->nullable()->after('authority');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        foreach (self::DOCTOR_TABLES as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('pmdc_number');
            });
        }
    }
};
