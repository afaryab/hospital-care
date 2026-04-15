<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bed_assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bed_id')->constrained('beds');
            $table->foreignId('ward_id')->constrained('wards');
            $table->foreignId('room_id')->constrained('rooms');
            $table->foreignId('patient_id')->constrained('patients');
            $table->foreignId('service_order_id')->constrained('service_orders');
            $table->foreignId('assigned_by')->constrained('users');
            $table->timestamp('admitted_at');
            $table->timestamp('discharged_at')->nullable();
            $table->string('status')->default('active')->comment('active|discharged|transferred');
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['bed_id', 'status']);
            $table->index(['patient_id', 'status']);
            $table->index(['service_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bed_assignments');
    }
};
