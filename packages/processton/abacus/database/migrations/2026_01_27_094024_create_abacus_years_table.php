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
        Schema::create('abacus_years', function (Blueprint $table) {
            $table->id();
            $table->dateTime('start_date')->comment('Start date of the year');
            $table->dateTime('end_date')->comment('End date of the year');
            $table->tinyInteger('status')->default(0)->comment('Status of the year: 0 = inactive, 1 = active, 2 = archived');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abacus_years');
    }
};
