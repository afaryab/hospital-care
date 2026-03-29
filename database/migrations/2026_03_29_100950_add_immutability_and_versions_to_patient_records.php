<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patient_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->json('snapshot');
            $table->string('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['patient_id', 'changed_at']);
        });

        Schema::create('treatment_record_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_record_id')->constrained('treatment_records')->onDelete('cascade');
            $table->json('snapshot');
            $table->string('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['treatment_record_id', 'changed_at']);
        });

        Schema::create('service_order_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->constrained('service_orders')->onDelete('cascade');
            $table->json('snapshot');
            $table->string('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['service_order_id', 'changed_at']);
        });

        Schema::table('patients', function (Blueprint $table): void {
            if (! Schema::hasColumn('patients', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('service_orders', function (Blueprint $table): void {
            if (! Schema::hasColumn('service_orders', 'deleted_at')) {
                $table->softDeletes();
            }
        });

        Schema::table('treatment_records', function (Blueprint $table): void {
            if (! Schema::hasColumn('treatment_records', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('treatment_records', function (Blueprint $table): void {
            if (Schema::hasColumn('treatment_records', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('service_orders', function (Blueprint $table): void {
            if (Schema::hasColumn('service_orders', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::table('patients', function (Blueprint $table): void {
            if (Schema::hasColumn('patients', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::dropIfExists('service_order_versions');
        Schema::dropIfExists('treatment_record_versions');
        Schema::dropIfExists('patient_versions');
    }
};
