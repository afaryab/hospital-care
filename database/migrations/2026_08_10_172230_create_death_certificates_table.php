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
        Schema::create('death_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->unique()->constrained('service_orders');
            $table->string('certificate_number')->unique();
            $table->date('date_of_death')->nullable();
            $table->time('time_of_death')->nullable();
            $table->string('place_of_death')->nullable();
            $table->string('manner_of_death')->nullable();
            $table->text('antecedent_cause')->nullable();
            $table->string('informant_name')->nullable();
            $table->string('informant_relation')->nullable();
            $table->string('informant_cnic')->nullable();
            $table->text('remarks')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('death_certificates');
    }
};
