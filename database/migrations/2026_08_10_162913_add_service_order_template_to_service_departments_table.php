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
        Schema::table('service_departments', function (Blueprint $table) {
            $table->string('service_order_template')->nullable()->after('have_composit_services');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_departments', function (Blueprint $table) {
            $table->dropColumn('service_order_template');
        });
    }
};
