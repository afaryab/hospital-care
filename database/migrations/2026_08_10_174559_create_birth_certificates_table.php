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
        Schema::create('birth_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->unique()->constrained('service_orders');
            $table->string('birth_certificate_number')->unique();
            $table->string('child_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->time('time_of_birth')->nullable();
            $table->string('gender')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->decimal('weight_at_birth', 5, 2)->nullable();
            $table->string('mother_name')->nullable();
            $table->string('mother_cnic')->nullable();
            $table->string('father_name')->nullable();
            $table->string('father_cnic')->nullable();
            $table->foreignId('attending_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('remarks')->nullable();
            $table->boolean('is_locked')->default(false);
            $table->dateTime('locked_at')->nullable();
            $table->foreignId('locked_by')->nullable()->constrained('users')->nullOnDelete();
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
        Schema::dropIfExists('birth_certificates');
    }
};
