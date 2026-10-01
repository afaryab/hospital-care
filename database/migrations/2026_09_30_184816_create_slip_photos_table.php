<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('slip_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->nullable()->constrained('patients');
            $table->foreignId('closing_id')->constrained('closings');
            $table->foreignId('transaction_id')->nullable()->constrained('transactions');
            $table->string('subject');
            $table->string('source');
            $table->foreignId('captured_by')->constrained('users');
            $table->timestamp('captured_at');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['closing_id', 'patient_id', 'transaction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('slip_photos');
    }
};
