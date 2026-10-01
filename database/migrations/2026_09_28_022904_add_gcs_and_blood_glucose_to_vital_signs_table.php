<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->unsignedTinyInteger('gcs')->nullable()->after('oxygen_saturation')->comment('Glasgow Coma Scale, 3-15');
            $table->decimal('blood_glucose', 6, 1)->nullable()->after('gcs')->comment('Blood sugar level, mg/dL');
        });
    }

    public function down(): void
    {
        Schema::table('vital_signs', function (Blueprint $table) {
            $table->dropColumn(['gcs', 'blood_glucose']);
        });
    }
};
