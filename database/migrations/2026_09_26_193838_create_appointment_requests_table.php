<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointment_requests', function (Blueprint $table) {
            $table->id();
            $table->uuid('reference')->unique();
            $table->string('name');
            $table->text('contact');
            $table->string('contact_hash', 64)->index();
            $table->char('gender', 1)->nullable();
            $table->unsignedSmallInteger('age_years')->nullable();
            $table->foreignId('service_id')->nullable()->constrained('services')->nullOnDelete();
            $table->date('preferred_date');
            $table->string('preferred_time', 20);
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->foreignId('appointment_id')->nullable()->constrained('appointments')->nullOnDelete();
            $table->foreignId('handled_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handled_at')->nullable();
            $table->string('rejection_reason')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'preferred_date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointment_requests');
    }
};
