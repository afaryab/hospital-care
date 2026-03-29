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
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->boolean('allow_petty_cash')->default(true)->after('pay_users')->comment('Allow payment via petty cash');
            $table->boolean('allow_voucher')->default(true)->after('allow_petty_cash')->comment('Allow payment via expense voucher');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn(['allow_petty_cash', 'allow_voucher']);
        });
    }
};
