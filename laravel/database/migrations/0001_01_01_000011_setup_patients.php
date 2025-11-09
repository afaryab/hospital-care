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
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('ps_number');

            $table->string('name');
            $table->string('gender')->nullable();

            $table->string('age_group')->nullable();
            $table->string('age_days')->nullable();
            $table->dateTime('age_dob')->nullable();
            
            $table->text('address')->nullable();
            $table->string('guardian')->nullable();
            $table->string('relation')->nullable();


            $table->string('contact')->index()->nullable();
            $table->string('cnic')->index()->nullable();

            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
