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
        Schema::create('referral_certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->unique()->constrained('service_orders');
            $table->string('referral_number')->unique();
            $table->string('receiving_facility_name')->nullable();
            $table->longText('notes')->nullable();
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
        Schema::dropIfExists('referral_certificates');
    }
};
