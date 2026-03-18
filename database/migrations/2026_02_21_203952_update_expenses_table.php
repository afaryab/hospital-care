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
            $table->string('type')->nullable()->after('name');
            $table->boolean('pay_doc')->default(false);
            $table->boolean('pay_others')->default(false);
            $table->boolean('pay_users')->default(false);
            $table->boolean('pay_patient')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expense_categories', function (Blueprint $table) {
            $table->dropColumn('type');
            $table->dropColumn('pay_doc');
            $table->dropColumn('pay_others');
            $table->dropColumn('pay_users');
            $table->dropColumn('pay_patient');
        });
    }
};
