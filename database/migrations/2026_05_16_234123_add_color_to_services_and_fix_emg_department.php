<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Color field for emergency triage coding on services
        Schema::table('services', function (Blueprint $table) {
            $table->string('color', 30)->nullable()->after('is_featured');
        });

        // Color field on resuscitation services
        Schema::table('service_recestations', function (Blueprint $table) {
            $table->string('color', 30)->nullable()->after('slug');
        });

        // Ensure EMG department exposes the Resuscitation section in billing
        DB::table('service_departments')
            ->where('slug', 'EMG')
            ->update(['have_composit_services' => 1]);
    }

    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        Schema::table('service_recestations', function (Blueprint $table) {
            $table->dropColumn('color');
        });

        DB::table('service_departments')
            ->where('slug', 'EMG')
            ->update(['have_composit_services' => 0]);
    }
};
