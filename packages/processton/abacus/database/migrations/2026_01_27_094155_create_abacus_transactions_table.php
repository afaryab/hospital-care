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
        Schema::create('abacus_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('abacus_incoming_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('abacus_chart_of_account_id')->constrained()->onDelete('cascade');
            $table->foreignId('abacus_year_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 15, 2);
            $table->date('date');
            $table->enum('entry_type', ['debit', 'credit']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abacus_transactions');
    }
};
