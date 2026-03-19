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
        // =====================================================================
        // Core Auth
        // =====================================================================

        Schema::create('images', function (Blueprint $table) {
            $table->id();
            $table->string('linked_to_model');
            $table->integer('linked_to_id');
            $table->string('path');
            $table->string('file_type')->nullable();
            $table->bigInteger('file_size')->nullable();
            $table->timestamps();

            $table->index(['linked_to_model', 'linked_to_id']);
        });

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('username')->nullable()->unique();
            $table->string('email')->nullable()->unique();
            $table->string('mobile')->nullable()->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->text('two_factor_secret')->nullable();
            $table->text('two_factor_recovery_codes')->nullable();
            $table->timestamp('two_factor_confirmed_at')->nullable();
            $table->dateTime('password_expired_at')->nullable();
            $table->dateTime('last_login')->nullable();
            $table->dateTime('last_activity')->nullable();
            $table->dateTime('last_login_attempt')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->integer('login_attempts')->default(0);
            $table->string('profile_img_path')->nullable();
            $table->foreignId('profile_img_id')->nullable()->constrained('images');
            $table->boolean('is_active')->default(true);
            $table->string('banned_message')->nullable();
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::table('images', function (Blueprint $table) {
            $table->foreignId('owner_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('uploader_id')->constrained('users')->onDelete('cascade');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // =====================================================================
        // Framework: Cache
        // =====================================================================

        Schema::create('cache', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->mediumText('value');
            $table->integer('expiration');
        });

        Schema::create('cache_locks', function (Blueprint $table) {
            $table->string('key')->primary();
            $table->string('owner');
            $table->integer('expiration');
        });

        // =====================================================================
        // Framework: Jobs
        // =====================================================================

        Schema::create('jobs', function (Blueprint $table) {
            $table->id();
            $table->string('queue')->index();
            $table->longText('payload');
            $table->unsignedTinyInteger('attempts');
            $table->unsignedInteger('reserved_at')->nullable();
            $table->unsignedInteger('available_at');
            $table->unsignedInteger('created_at');
        });

        Schema::create('job_batches', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->integer('total_jobs');
            $table->integer('pending_jobs');
            $table->integer('failed_jobs');
            $table->longText('failed_job_ids');
            $table->mediumText('options')->nullable();
            $table->integer('cancelled_at')->nullable();
            $table->integer('created_at');
            $table->integer('finished_at')->nullable();
        });

        Schema::create('failed_jobs', function (Blueprint $table) {
            $table->id();
            $table->string('uuid')->unique();
            $table->text('connection');
            $table->text('queue');
            $table->longText('payload');
            $table->longText('exception');
            $table->timestamp('failed_at')->useCurrent();
        });

        // =====================================================================
        // App Configuration
        // =====================================================================

        Schema::create('instance_variables', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('group')->default('default');
            $table->string('variable');
            $table->string('value_type');
            $table->string('value_string')->nullable();
            $table->string('value_composit')->nullable();
            $table->timestamps();
        });

        // =====================================================================
        // Patients
        // =====================================================================

        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->string('ps_number')->index();
            $table->string('name');
            $table->string('gender')->nullable();
            $table->string('age_group')->nullable();
            $table->string('age_days')->nullable();
            $table->dateTime('age_dob')->nullable();
            $table->text('address')->nullable();
            $table->string('guardian')->nullable();
            $table->string('relation')->nullable();
            $table->string('contact')->index()->nullable();
            $table->string('cnic')->index()->nullable();
            $table->timestamps();
        });

        Schema::create('panels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // =====================================================================
        // Services
        // =====================================================================

        Schema::create('service_departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug');
            $table->string('image');
            $table->tinyInteger('have_composit_services');
            $table->timestamps();
        });

        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('service_department_id')->constrained('service_departments')->onDelete('cascade');
            $table->decimal('charges', 10, 2);
            $table->tinyInteger('charges_include_tax');
            $table->double('tax_rate');
            $table->string('slug')->index();
            $table->tinyInteger('have_service_provider')->default(0);
            $table->json('service_provider_types')->nullable();
            $table->tinyInteger('is_composit_service')->default(0);
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('old_id')->nullable()->index();
            $table->tinyInteger('is_featured')->default(0);
            $table->tinyInteger('generate_service_order')->default(0);
            $table->timestamps();
        });

        Schema::create('service_recestations', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('service_department_id')->constrained('service_departments')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->decimal('charges', 10, 2);
            $table->tinyInteger('charges_include_tax');
            $table->double('tax_rate');
            $table->string('slug')->index();
            $table->tinyInteger('have_service_provider')->default(0);
            $table->json('service_provider_types')->nullable();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->integer('old_id')->nullable()->index();
            $table->timestamps();
        });

        Schema::create('service_orders', function (Blueprint $table) {
            $table->id();
            $table->string('type')->index();
            $table->string('status')->default('open');
            $table->dateTime('closed_at')->nullable();
            $table->string('so_number')->unique();
            $table->string('token', 64)->nullable();
            $table->string('so_short')->unique();
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('service_recestation_id')->nullable()->constrained('service_recestations')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->tinyInteger('is_composit')->default(0);
            $table->text('notes')->nullable();
            $table->json('notes_json')->nullable();
            $table->morphs('payee');
            $table->timestamps();

            $table->index(['patient_id', 'type'], 'service_orders_patient_type_index');
            $table->index(['type', 'status', 'service_id', 'created_at'], 'idx_service_orders_type_status_service_created');
        });

        // =====================================================================
        // User Profiles
        // =====================================================================

        Schema::create('administrators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('executive, administrator, superadmin');
            $table->timestamps();
        });

        Schema::create('accountants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('receptionists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('opd_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('ind_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('emergency_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('dentists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('ultrasound_doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('xray_technicians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('nursing_staff', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('authority')->default('assistant')->comment('assistant, manager');
            $table->timestamps();
        });

        Schema::create('patient_managers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->timestamps();
        });

        // =====================================================================
        // Expenses
        // =====================================================================

        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->string('name');
            $table->string('type')->nullable();
            $table->boolean('pay_doc')->default(false);
            $table->boolean('pay_others')->default(false);
            $table->boolean('pay_users')->default(false);
            $table->boolean('pay_patient')->default(false);
            $table->timestamps();
        });

        Schema::create('expense_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('vc_number')->unique();
            $table->integer('old_id')->nullable();
            $table->foreignId('exp_category_id')->constrained('expense_categories')->onDelete('cascade');
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->onDelete('cascade');
            $table->foreignId('payed_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('payed_to_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('notes')->nullable();
            $table->timestamps();
            // transaction_id and transaction_element_id added after transactions/transaction_elements
        });

        // =====================================================================
        // Receptions & Financial
        // =====================================================================

        Schema::create('receptions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->json('allowed_departments')->nullable();
            $table->tinyInteger('is_allowed_to_pay_voucher');
            $table->tinyInteger('is_allowed_to_pay_from_petty_cash');
            $table->tinyInteger('is_cash_allowed');
            $table->tinyInteger('is_cheques_allowed');
            $table->tinyInteger('is_card_allowed');
            $table->timestamps();
        });

        Schema::create('closings', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->string('ct_number')->unique();
            $table->foreignId('reception_id')->constrained('receptions')->onDelete('cascade');
            $table->foreignId('receptionist_id')->constrained('users')->onDelete('cascade');
            $table->string('status')->default('OPEN');
            $table->decimal('opening_amount', 10, 2)->default(0);
            $table->decimal('closing_amount', 10, 2)->default(0);
            $table->decimal('closing_amount_cash', 10, 2)->default(0);
            $table->decimal('closing_amount_cheque', 10, 2)->default(0);
            $table->decimal('closing_amount_card', 10, 2)->default(0);
            $table->decimal('expense_payed', 10, 2)->default(0);
            $table->dateTime('closed_at')->nullable();
            $table->dateTime('cash_recieving_time')->nullable();
            $table->decimal('amount_received', 12, 2)->nullable();
            $table->foreignId('reported_by')->nullable()->constrained('users');
            $table->timestamps();

            $table->index(['status', 'receptionist_id']);
        });

        Schema::create('receaveables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->onDelete('cascade');
            $table->foreignId('panel_id')->nullable()->references('id')->on('panels')->onDelete('set null');
            $table->decimal('amount', 15, 2);
            $table->decimal('orignal_amount', 15, 2)->nullable();
            $table->date('due_date')->nullable();
            $table->string('status')->default('unpaid');
            $table->timestamps();

            $table->index('status');
            // transaction_id added after transactions table
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->string('tr_number')->unique();
            $table->integer('old_id')->nullable();
            $table->foreignId('closing_id')->constrained('closings')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('receaveable_id')->nullable()->constrained('receaveables');
            $table->foreignId('panel_id')->nullable()->references('id')->on('panels')->onDelete('set null');
            $table->tinyInteger('is_processed')->default(1);
            $table->string('type')->default('CASH');
            $table->string('income_or_expense');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('amount_alphabetical')->default('Zero');
            $table->decimal('orignal_amount', 10, 2)->default(0);
            $table->decimal('customer_payed', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->decimal('edited_amount', 10, 2)->nullable();
            $table->boolean('is_refunded')->default(false);
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->nullOnDelete();
            $table->foreignId('exp_voucher_id')->nullable()->constrained('expense_vouchers')->nullOnDelete();
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('income_or_expense', 'idx_tr_income_or_expense');
            $table->index('created_at', 'idx_tr_created_at');
            $table->index(['income_or_expense', 'closing_id'], 'idx_tr_income_closing');
        });

        Schema::create('transaction_elements', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->foreignId('closing_id')->constrained('closings')->onDelete('cascade');
            $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('service_id')->nullable()->constrained('services')->onDelete('cascade');
            $table->foreignId('service_recestation_id')->nullable()->constrained('service_recestations')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');
            $table->foreignId('expense_category_id')->nullable()->constrained('expense_categories')->onDelete('cascade');
            $table->foreignId('exp_voucher_id')->nullable()->constrained('expense_vouchers')->onDelete('cascade');
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->onDelete('cascade');
            $table->string('type');
            $table->string('income_or_expense');
            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('orignal_amount', 10, 2)->default(0);
            $table->decimal('customer_payed', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            $table->decimal('edited_amount', 10, 2)->nullable();
            $table->foreignId('refunded_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->foreignId('expense_service_order_id')->nullable()->constrained('service_orders')->onDelete('set null');
            $table->string('notes')->nullable();
            $table->timestamps();

            $table->index('income_or_expense', 'idx_te_income_or_expense');
            $table->index('created_at', 'idx_te_created_at');
            $table->index(['income_or_expense', 'created_at'], 'idx_te_income_created');
            $table->index(['income_or_expense', 'created_at', 'closing_id'], 'idx_te_report_query');
        });

        // =====================================================================
        // Deferred Foreign Keys (circular dependencies)
        // =====================================================================

        Schema::table('receaveables', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
        });

        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->foreignId('transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->foreignId('transaction_element_id')->nullable()->constrained('transaction_elements')->nullOnDelete();
        });

        // =====================================================================
        // Pivot Tables
        // =====================================================================

        Schema::create('expense_voucher_service_order', function (Blueprint $table) {
            $table->id();
            $table->foreignId('expense_voucher_id')->constrained('expense_vouchers')->onDelete('cascade');
            $table->foreignId('service_order_id')->constrained('service_orders')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['expense_voucher_id', 'service_order_id'], 'ev_so_unique');
        });

        // =====================================================================
        // System / Migration Tooling
        // =====================================================================

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

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('migration_logs');
        Schema::dropIfExists('upgrade_processes');
        Schema::dropIfExists('expense_voucher_service_order');
        Schema::dropIfExists('transaction_elements');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('receaveables');
        Schema::dropIfExists('closings');
        Schema::dropIfExists('receptions');
        Schema::dropIfExists('expense_vouchers');
        Schema::dropIfExists('expense_categories');
        Schema::dropIfExists('patient_managers');
        Schema::dropIfExists('nursing_staff');
        Schema::dropIfExists('xray_technicians');
        Schema::dropIfExists('ultrasound_doctors');
        Schema::dropIfExists('dentists');
        Schema::dropIfExists('emergency_doctors');
        Schema::dropIfExists('ind_doctors');
        Schema::dropIfExists('opd_doctors');
        Schema::dropIfExists('receptionists');
        Schema::dropIfExists('accountants');
        Schema::dropIfExists('administrators');
        Schema::dropIfExists('service_orders');
        Schema::dropIfExists('service_recestations');
        Schema::dropIfExists('services');
        Schema::dropIfExists('service_departments');
        Schema::dropIfExists('panels');
        Schema::dropIfExists('patients');
        Schema::dropIfExists('instance_variables');
        Schema::dropIfExists('failed_jobs');
        Schema::dropIfExists('job_batches');
        Schema::dropIfExists('jobs');
        Schema::dropIfExists('cache_locks');
        Schema::dropIfExists('cache');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('images');

        Schema::enableForeignKeyConstraints();
    }
};
