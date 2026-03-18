<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('receaveables', function (Blueprint $table) {
            $table->decimal('orignal_amount', 15, 2)->nullable()->after('amount');
        });

        // Backfill existing records: set orignal_amount = amount for records that don't have it
        DB::table('receaveables')->whereNull('orignal_amount')->update([
            'orignal_amount' => DB::raw('amount'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receaveables', function (Blueprint $table) {
            $table->dropColumn('orignal_amount');
        });
    }
};
