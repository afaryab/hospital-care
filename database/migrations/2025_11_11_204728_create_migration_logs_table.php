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
        Schema::create('migration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('migration_step')->index(); // e.g., 'users', 'transactions', etc.
            $table->string('action_type')->index(); // 'skipped', 'duplicated', 'error', 'warning', 'success'
            $table->string('old_table')->nullable(); // source table name
            $table->string('old_record_id')->nullable()->index(); // source record ID
            $table->string('new_table')->nullable(); // destination table name  
            $table->unsignedBigInteger('new_record_id')->nullable()->index(); // new record ID
            $table->string('reason')->nullable(); // reason for skip/error
            $table->text('old_data')->nullable(); // JSON of old record data
            $table->text('new_data')->nullable(); // JSON of new record data
            $table->text('error_details')->nullable(); // detailed error information
            $table->decimal('old_amount', 15, 2)->nullable(); // for financial tracking
            $table->decimal('new_amount', 15, 2)->nullable(); // for financial tracking
            $table->json('validation_errors')->nullable(); // validation issues
            $table->timestamp('migration_time')->useCurrent(); // when this occurred
            $table->string('batch_id')->nullable()->index(); // to group related operations
            $table->timestamps();
            
            // Indexes for performance
            $table->index(['migration_step', 'action_type']);
            $table->index(['old_table', 'old_record_id']);
            $table->index(['migration_time']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('migration_logs');
    }
};
