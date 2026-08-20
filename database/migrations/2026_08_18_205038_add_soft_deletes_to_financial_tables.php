<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('closings', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->softDeletes();
        });

        Schema::table('receaveables', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('closings', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });

        Schema::table('receaveables', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
};
