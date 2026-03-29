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
        $indexName = 'idx_service_orders_type_status_service_created';

        $indexes = Schema::getIndexes('service_orders');
        $indexExists = collect($indexes)->contains(fn ($index) => $index['name'] === $indexName);

        if (! $indexExists) {
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

        $indexes = Schema::getIndexes('service_orders');
        $indexExists = collect($indexes)->contains(fn ($index) => $index['name'] === $indexName);

        if ($indexExists) {
            Schema::table('service_orders', function (Blueprint $table) use ($indexName) {
                $table->dropIndex($indexName);
            });
        }
    }
};
