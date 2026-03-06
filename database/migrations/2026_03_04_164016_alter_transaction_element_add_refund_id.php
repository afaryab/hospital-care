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
        Schema::table('transactions', function (Blueprint $table){
            $table->boolean('is_refunded')->default(0);
        });

        Schema::table('transaction_elements', function (Blueprint $table){
            $table->foreignId('refunded_transaction_id')->nullable()->constrained('transactions', 'id')->onDelete('set null');
            $table->foreignId('expense_service_order_id')->nullable()->constrained('service_orders', 'id')->onDelete('set null');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table){
            $table->dropColumn('is_refunded');
        });

        Schema::table('transaction_elements', function (Blueprint $table){
            $table->dropForeign(['refunded_transaction_id']);
            $table->dropColumn('refunded_transaction_id');

            $table->dropForeign(['expense_service_order_id']);
            $table->dropColumn('expense_service_order_id');
        });
    }
};
