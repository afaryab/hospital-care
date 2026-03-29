<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // Phase 1.3 — Hospital Settings
        // =====================================================================

        Schema::create('hospital_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // =====================================================================
        // Phase 1.4 — Consent Management
        // =====================================================================

        Schema::create('consents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->onDelete('set null');
            $table->string('consent_type')->comment('treatment, procedure, data_sharing');
            $table->string('consent_method')->comment('digital_checkbox, paper_signed, verbal_recorded');
            $table->dateTime('consented_at');
            $table->foreignId('recorded_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['patient_id', 'consent_type']);
        });

        // =====================================================================
        // Phase 1.5 — Treatment Records & Vital Signs
        // =====================================================================

        Schema::create('icd10_codes', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique()->index();
            $table->string('description');
            $table->string('category')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('treatment_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_order_id')->unique()->constrained('service_orders')->onDelete('cascade');
            $table->foreignId('department_id')->constrained('service_departments');
            $table->foreignId('treating_doctor_id')->constrained('users');
            $table->text('chief_complaint')->nullable();
            $table->text('history_of_present_illness')->nullable();
            $table->json('examination_findings')->nullable();
            $table->string('diagnosis_code')->nullable();
            $table->text('diagnosis_text')->nullable();
            $table->text('treatment_plan')->nullable();
            $table->json('prescriptions')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->string('outcome')->nullable()->comment('improved, unchanged, deteriorated, referred, expired');
            $table->string('referral_to')->nullable();
            $table->json('department_specific_data')->nullable();
            $table->dateTime('treated_at')->nullable();
            $table->foreignId('recorded_by')->constrained('users');
            $table->boolean('is_finalized')->default(false);
            $table->dateTime('finalized_at')->nullable();
            $table->timestamps();

            $table->index(['department_id', 'is_finalized']);
        });

        Schema::create('vital_signs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('treatment_record_id')->constrained('treatment_records')->onDelete('cascade');
            $table->decimal('temperature', 5, 2)->nullable();
            $table->integer('blood_pressure_systolic')->nullable();
            $table->integer('blood_pressure_diastolic')->nullable();
            $table->integer('pulse_rate')->nullable();
            $table->integer('respiratory_rate')->nullable();
            $table->decimal('oxygen_saturation', 5, 2)->nullable();
            $table->decimal('weight', 6, 2)->nullable();
            $table->decimal('height', 5, 2)->nullable();
            $table->dateTime('recorded_at');
            $table->foreignId('recorded_by')->constrained('users');
            $table->timestamps();
        });

        // =====================================================================
        // Phase 1.6 — Stock Tracking
        // =====================================================================

        Schema::create('stock_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('parent_id')->nullable()->constrained('stock_categories')->onDelete('set null');
            $table->boolean('is_medicine')->default(false);
            $table->timestamps();
        });

        Schema::create('stock_items', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('sku')->unique();
            $table->foreignId('category_id')->constrained('stock_categories');
            $table->string('unit')->comment('pcs, ml, mg, box, strip, bottle, vial');
            $table->integer('reorder_level')->default(0);
            $table->string('default_vendor')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('po_number')->unique();
            $table->string('vendor_name');
            $table->string('status')->default('draft')->comment('draft, approved, received, cancelled');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('stock_item_id')->constrained('stock_items');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 10, 2);
            $table->decimal('total_cost', 12, 2);
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->timestamps();
        });

        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('stock_item_id')->constrained('stock_items');
            $table->string('type')->comment('IN, OUT');
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_cost', 10, 2)->nullable();
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('department_id')->nullable()->constrained('service_departments')->onDelete('set null');
            $table->string('batch_number')->nullable();
            $table->date('expiry_date')->nullable();
            $table->foreignId('moved_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['stock_item_id', 'type']);
            $table->index(['reference_type', 'reference_id']);
        });

        Schema::create('service_stock_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('stock_item_id')->constrained('stock_items')->onDelete('cascade');
            $table->decimal('quantity_consumed', 10, 2);
            $table->timestamps();

            $table->unique(['service_id', 'stock_item_id']);
        });

        // =====================================================================
        // Phase 1.7 — Asset Tracking
        // =====================================================================

        Schema::create('asset_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('depreciation_method')->default('straight_line')->comment('straight_line, declining_balance, none');
            $table->integer('useful_life_years')->nullable();
            $table->timestamps();
        });

        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('asset_number')->unique();
            $table->string('name');
            $table->foreignId('category_id')->constrained('asset_categories');
            $table->string('serial_number')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->default(0);
            $table->string('vendor_name')->nullable();
            $table->date('warranty_expiry')->nullable();
            $table->foreignId('assigned_to_department_id')->nullable()->constrained('service_departments')->onDelete('set null');
            $table->foreignId('assigned_to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('location')->nullable();
            $table->string('status')->default('active')->comment('active, under_maintenance, retired, disposed');
            $table->date('disposed_at')->nullable();
            $table->text('disposal_reason')->nullable();
            $table->decimal('disposal_value', 12, 2)->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('asset_assignment_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('from_department_id')->nullable()->constrained('service_departments')->onDelete('set null');
            $table->foreignId('to_department_id')->nullable()->constrained('service_departments')->onDelete('set null');
            $table->foreignId('from_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('to_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('transferred_by')->constrained('users');
            $table->text('notes')->nullable();
            $table->dateTime('transferred_at');
            $table->timestamps();
        });

        Schema::create('asset_maintenance_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->string('type')->comment('preventive, corrective, calibration');
            $table->text('description')->nullable();
            $table->decimal('cost', 10, 2)->default(0);
            $table->string('performed_by')->nullable();
            $table->date('scheduled_date')->nullable();
            $table->date('completed_date')->nullable();
            $table->date('next_maintenance_date')->nullable();
            $table->timestamps();
        });

        Schema::create('asset_depreciation_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->integer('period_year');
            $table->integer('period_month');
            $table->decimal('depreciation_amount', 12, 2);
            $table->decimal('accumulated_depreciation', 12, 2);
            $table->decimal('book_value', 12, 2);
            $table->timestamps();

            $table->unique(['asset_id', 'period_year', 'period_month'], 'ade_asset_year_month_unique');
        });

        // =====================================================================
        // Phase 1.8 — User Tasking
        // =====================================================================

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('task_number')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('priority')->default('medium')->comment('low, medium, high, urgent');
            $table->string('status')->default('todo')->comment('todo, in_progress, blocked, completed, cancelled');
            $table->foreignId('assigned_to')->constrained('users');
            $table->foreignId('assigned_by')->constrained('users');
            $table->foreignId('department_id')->nullable()->constrained('service_departments')->onDelete('set null');
            $table->dateTime('due_date')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->text('completion_notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['assigned_to', 'status']);
            $table->index('status');
        });

        Schema::create('task_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained('tasks')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->text('body');
            $table->timestamps();
        });

        // =====================================================================
        // Phase 1.9 — User Payroll
        // =====================================================================

        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('basic_salary', 12, 2)->default(0);
            $table->decimal('housing_allowance', 10, 2)->default(0);
            $table->decimal('medical_allowance', 10, 2)->default(0);
            $table->decimal('transport_allowance', 10, 2)->default(0);
            $table->json('other_allowances')->nullable();
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'effective_from', 'effective_to']);
        });

        Schema::create('payroll_periods', function (Blueprint $table) {
            $table->id();
            $table->string('period_number')->unique();
            $table->integer('year');
            $table->integer('month');
            $table->string('status')->default('draft')->comment('draft, calculated, approved, paid, closed');
            $table->foreignId('processed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->dateTime('approved_at')->nullable();
            $table->dateTime('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['year', 'month']);
        });

        Schema::create('payslip_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('salary_structure_id')->constrained('salary_structures');
            $table->decimal('gross_salary', 12, 2)->default(0);
            $table->json('deductions')->nullable();
            $table->decimal('total_deductions', 12, 2)->default(0);
            $table->decimal('net_salary', 12, 2)->default(0);
            $table->string('payment_method')->nullable()->comment('cash, bank_transfer, cheque');
            $table->dateTime('paid_at')->nullable();
            $table->foreignId('paid_via_voucher_id')->nullable()->constrained('expense_vouchers')->onDelete('set null');
            $table->timestamps();

            $table->unique(['payroll_period_id', 'user_id']);
        });

        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->decimal('amount', 12, 2);
            $table->foreignId('granted_by')->constrained('users');
            $table->date('granted_at');
            $table->decimal('deduction_per_month', 10, 2);
            $table->decimal('remaining_balance', 12, 2);
            $table->string('status')->default('active')->comment('active, fully_recovered, written_off');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('salary_advances');
        Schema::dropIfExists('payslip_entries');
        Schema::dropIfExists('payroll_periods');
        Schema::dropIfExists('salary_structures');
        Schema::dropIfExists('task_comments');
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('asset_depreciation_entries');
        Schema::dropIfExists('asset_maintenance_logs');
        Schema::dropIfExists('asset_assignment_logs');
        Schema::dropIfExists('assets');
        Schema::dropIfExists('asset_categories');
        Schema::dropIfExists('service_stock_item');
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('stock_items');
        Schema::dropIfExists('stock_categories');
        Schema::dropIfExists('vital_signs');
        Schema::dropIfExists('treatment_records');
        Schema::dropIfExists('icd10_codes');
        Schema::dropIfExists('consents');
        Schema::dropIfExists('hospital_settings');

        Schema::enableForeignKeyConstraints();
    }
};
