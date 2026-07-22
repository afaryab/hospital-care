<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('treatment_records', function (Blueprint $table) {
            $table->foreignId('triage_id')->nullable()->after('department_id')->constrained('triages')->nullOnDelete();
            $table->json('dental_chart')->nullable()->after('department_specific_data');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('treatment_records', function (Blueprint $table) {
            $table->dropConstrainedForeignId('triage_id');
            $table->dropColumn('dental_chart');
        });
    }
};
