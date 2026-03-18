<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('transaction_element_id')->nullable()->constrained('transaction_elements')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['transaction_element_id']);
            $table->dropColumn(['transaction_id', 'transaction_element_id']);
        });
    }
};
