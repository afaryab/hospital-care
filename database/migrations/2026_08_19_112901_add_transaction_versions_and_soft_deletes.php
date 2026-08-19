<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extends the existing PatientVersion/ServiceOrderVersion/
     * TreatmentRecordVersion pattern to Transaction, and closes the same
     * "Soft Deletes Only" gap those records were already given (see
     * 2026_03_29_100950_add_immutability_and_versions_to_patient_records.php)
     * — transactions.destroy() currently performs a hard delete on a
     * financial record, in violation of the project's data-integrity rules.
     */
    public function up(): void
    {
        Schema::create('transaction_versions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->json('snapshot');
            $table->string('change_reason')->nullable();
            $table->foreignId('changed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('changed_at');
            $table->timestamps();

            $table->index(['transaction_id', 'changed_at']);
        });

        Schema::table('transactions', function (Blueprint $table): void {
            if (! Schema::hasColumn('transactions', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table): void {
            if (Schema::hasColumn('transactions', 'deleted_at')) {
                $table->dropSoftDeletes();
            }
        });

        Schema::dropIfExists('transaction_versions');
    }
};
