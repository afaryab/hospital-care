<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expense_voucher_service_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_voucher_id')->constrained('expense_vouchers')->onDelete('cascade');
            $table->foreignId('service_order_id')->constrained('service_orders')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['expense_voucher_id', 'service_order_id'], 'ev_so_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expense_voucher_service_order');
    }
};
