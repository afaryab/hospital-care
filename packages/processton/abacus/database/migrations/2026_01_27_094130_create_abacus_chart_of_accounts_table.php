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
        Schema::create('abacus_chart_of_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Name of the account');
            $table->string('code')->unique()->comment('Unique code for the account');
            $table->enum('base_type', ['asset', 'liability', 'equity', 'income', 'expense'])->comment('Type of the account (e.g., asset, liability, equity, revenue, expense)');
            $table->string('type')->nullable();
            $table->string('description')->nullable()->comment('Description of the account');
            $table->foreignId('parent_id')->nullable()->constrained('abacus_chart_of_accounts')->onDelete('cascade');
            $table->boolean('is_group')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('abacus_chart_of_accounts');
    }
};
