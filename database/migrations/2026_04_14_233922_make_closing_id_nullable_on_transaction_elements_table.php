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
        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->dropForeign(['closing_id']);
            $table->unsignedBigInteger('closing_id')->nullable()->change();
            $table->foreign('closing_id')->references('id')->on('closings')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->dropForeign(['closing_id']);
            $table->unsignedBigInteger('closing_id')->nullable(false)->change();
            $table->foreign('closing_id')->references('id')->on('closings')->onDelete('cascade');
        });
    }
};
