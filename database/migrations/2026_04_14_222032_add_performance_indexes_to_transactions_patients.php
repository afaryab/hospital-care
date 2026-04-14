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
        // transactions: covering index for admin table (whereNull closing_id + order by id desc)
        // and separate indexes for filter/sort queries
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('type', 'idx_tr_type');
            $table->index(['closing_id', 'id'], 'idx_tr_closing_id_id');
            $table->index(['income_or_expense', 'id'], 'idx_tr_income_id');
        });

        // patients: name index for Filament search on patient.name
        Schema::table('patients', function (Blueprint $table) {
            $table->index('name', 'idx_patients_name');
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_tr_type');
            $table->dropIndex('idx_tr_closing_id_id');
            $table->dropIndex('idx_tr_income_id');
        });

        Schema::table('patients', function (Blueprint $table) {
            $table->dropIndex('idx_patients_name');
        });
    }
};
