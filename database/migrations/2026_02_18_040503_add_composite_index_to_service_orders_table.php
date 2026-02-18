<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $indexName = 'idx_service_orders_type_status_service_created';

        // Only create the index if it doesn't already exist
        $existing = DB::select("SHOW INDEX FROM service_orders WHERE Key_name = ?", [$indexName]);
        if (count($existing) === 0) {
            Schema::table('service_orders', function (Blueprint $table) use ($indexName) {
                $table->index(['type', 'status', 'service_id', 'created_at'], $indexName);
            });
        }

        Schema::table('service_orders', function (Blueprint $table) {
            $table->string('token', 64)->nullable()->after('so_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {


        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropColumn('token');
        });


        $indexName = 'idx_service_orders_type_status_service_created';

        // Only drop the index if it exists
        $existing = DB::select("SHOW INDEX FROM service_orders WHERE Key_name = ?", [$indexName]);
        if (count($existing) > 0) {
            Schema::table('service_orders', function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
