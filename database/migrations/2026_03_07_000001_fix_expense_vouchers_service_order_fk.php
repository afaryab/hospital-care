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
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('exp_voucher_id')->nullable()->constrained('expense_vouchers')->nullOnDelete();
            $table->string('notes')->nullable();
        });

        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->string('notes')->nullable();
        });

        // add notes to expense vouchers
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->string('notes')->nullable();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropColumn(['notes']);
        });

        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->dropColumn(['notes']);
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['expense_category_id']);
            $table->dropForeign(['exp_voucher_id']);
            $table->dropColumn(['expense_category_id', 'exp_voucher_id', 'notes']);
        });
    }
};
