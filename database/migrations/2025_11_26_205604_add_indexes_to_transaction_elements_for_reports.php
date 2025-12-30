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
        Schema::table('transaction_elements', function (Blueprint $table) {
            // Add indexes for report filtering and joining
            $table->index('income_or_expense', 'idx_income_or_expense');
            $table->index('created_at', 'idx_created_at');
            $table->index(['income_or_expense', 'created_at'], 'idx_income_created');
            $table->index('closing_id', 'idx_closing_id');
            $table->index('service_id', 'idx_service_id');
            $table->index('doctor_id', 'idx_doctor_id');
            $table->index('patient_id', 'idx_patient_id');
            $table->index('service_order_id', 'idx_service_order_id');
            $table->index('transaction_id', 'idx_transaction_id');
            
            // Composite index for common report queries
            $table->index(['income_or_expense', 'created_at', 'closing_id'], 'idx_report_query');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transaction_elements', function (Blueprint $table) {
            // Drop indexes in reverse order
            $table->dropIndex('idx_report_query');
            $table->dropIndex('idx_transaction_id');
            $table->dropIndex('idx_service_order_id');
            $table->dropIndex('idx_patient_id');
            $table->dropIndex('idx_doctor_id');
            $table->dropIndex('idx_service_id');
            $table->dropIndex('idx_closing_id');
            $table->dropIndex('idx_income_created');
            $table->dropIndex('idx_created_at');
            $table->dropIndex('idx_income_or_expense');
        });
    }
};
