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
        Schema::create('instance_variables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('group')->default('default');
            $table->string('variable');
            $table->string('value_type');
            $table->string('value_string')->nullable();
            $table->string('value_composit')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instance_variables');
    }
};
