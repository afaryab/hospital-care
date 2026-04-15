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
        Schema::table('abacus_incomings', function (Blueprint $table) {
            $table->nullableMorphs('source', 'abacus_incomings_source_index');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('abacus_incomings', function (Blueprint $table) {
            $table->dropMorphs('source', 'abacus_incomings_source_index');
        });
    }
};
