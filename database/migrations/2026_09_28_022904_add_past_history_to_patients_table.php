<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Patient-level past medical history printed on the triage note.
     * null = not asked / unknown, true = Yes, false = No.
     */
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->boolean('history_htn')->nullable();
            $table->boolean('history_dm')->nullable();
            $table->boolean('history_asthma')->nullable();
            $table->boolean('history_ihd')->nullable();
            $table->text('allergies')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn(['history_htn', 'history_dm', 'history_asthma', 'history_ihd', 'allergies']);
        });
    }
};
