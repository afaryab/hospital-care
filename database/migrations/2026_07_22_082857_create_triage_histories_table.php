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
        Schema::create('triage_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_record_id')->constrained('treatment_records')->onDelete('cascade');
            $table->foreignId('service_order_id')->constrained('service_orders')->onDelete('cascade');
            $table->foreignId('old_triage_id')->nullable()->constrained('triages')->nullOnDelete();
            $table->foreignId('new_triage_id')->constrained('triages');
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['service_order_id', 'changed_at']);
            $table->index(['new_triage_id', 'changed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('triage_histories');
    }
};
