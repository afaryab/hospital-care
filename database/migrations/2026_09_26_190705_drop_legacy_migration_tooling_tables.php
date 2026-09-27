<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Drop the tables used only by the old-HIMS migration tooling, which has been removed.
     */
    public function up(): void
    {
        Schema::dropIfExists('migration_logs');
        Schema::dropIfExists('upgrade_processes');
    }

    /**
     * Recreate the table structure only; dropped rows cannot be restored.
     */
    public function down(): void
    {
        Schema::create('upgrade_processes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->integer('value')->default(0);
            $table->timestamps();
        });

        Schema::create('migration_logs', function (Blueprint $table) {
            $table->id();
            $table->string('migration_step')->index();
            $table->string('action_type')->index();
            $table->string('old_table')->nullable();
            $table->string('old_record_id')->nullable()->index();
            $table->string('new_table')->nullable();
            $table->unsignedBigInteger('new_record_id')->nullable()->index();
            $table->string('reason')->nullable();
            $table->text('old_data')->nullable();
            $table->text('new_data')->nullable();
            $table->text('error_details')->nullable();
            $table->decimal('old_amount', 15, 2)->nullable();
            $table->decimal('new_amount', 15, 2)->nullable();
            $table->json('validation_errors')->nullable();
            $table->timestamp('migration_time')->useCurrent();
            $table->string('batch_id')->nullable()->index();
            $table->timestamps();

            $table->index(['migration_step', 'action_type']);
            $table->index(['old_table', 'old_record_id']);
            $table->index('migration_time');
        });
    }
};
