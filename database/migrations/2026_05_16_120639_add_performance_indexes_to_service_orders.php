<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // service_orders.created_at — the admin list page filters by date range;
        // without this the query does a full table scan.
        Schema::table('service_orders', function (Blueprint $table) {
            $table->index('created_at', 'idx_so_created_at');
            $table->index('status', 'idx_so_status');
        });

        // transaction_elements — the withSum subqueries filter by service_order_id
        // and income_or_expense then sum amount; this covering index serves both.
        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->index(
                ['service_order_id', 'income_or_expense', 'amount'],
                'idx_te_so_income_amount'
            );
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropIndex('idx_so_created_at');
            $table->dropIndex('idx_so_status');
        });

        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->dropIndex('idx_te_so_income_amount');
        });
    }
};
