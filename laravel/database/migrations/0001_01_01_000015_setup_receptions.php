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
            $table->string('ct_number')->unique()->index();
            $table->foreignId('reception_id')->constrained('receptions')->onDelete('cascade');
            $table->foreignId('receptionist_id')->constrained('users')->onDelete('cascade');

            $table->string('status')->default('OPEN');
            $table->decimal('opening_amount', 10, 2)->default(0);
            $table->decimal('closing_amount', 10, 2)->default(0);
            $table->decimal('closing_amount_cash', 10, 2)->default(0);
            $table->decimal('closing_amount_cheque', 10, 2)->default(0);
            $table->decimal('closing_amount_card', 10, 2)->default(0);
            $table->decimal('expense_payed', 10, 2)->default(0);
            $table->dateTime('cash_recieving_time')->nullable();
            $table->timestamps();
        });

        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->foreignId('closing_id')->constrained('closings')->onDelete('cascade');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade');

            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');

            $table->tinyInteger('is_processed')->default(1);

            $table->string('type')->default('CASH');
            $table->string('income_or_expense');
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('amount_alphabetical')->default('Zero');
            $table->decimal('orignal_amount', 10, 2)->default(0);
            $table->decimal('customer_payed', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            
            $table->decimal('edited_amount', 10, 2)->nullable();
            $table->timestamps();
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
            $table->foreignId('expense_id')->nullable()->constrained('expenses')->onDelete('cascade');
            $table->foreignId('exp_voucher_id')->nullable()->constrained('expense_vouchers')->onDelete('cascade');
            $table->foreignId('service_order_id')->nullable()->constrained('service_orders')->onDelete('cascade');

            $table->string('type');
            $table->string('income_or_expense');

            $table->decimal('amount', 10, 2)->default(0);
            $table->decimal('orignal_amount', 10, 2)->default(0);
            $table->decimal('customer_payed', 10, 2)->default(0);
            $table->decimal('change', 10, 2)->default(0);
            
            $table->decimal('edited_amount', 10, 2)->nullable();
            $table->timestamps();
        });
        // Schema::table('service_orders', function (Blueprint $table) {
        //     $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade')->after('so_number');
        //     $table->foreignId('transaction_element_id')->constrained('transaction_elements')->onDelete('cascade')->after('transaction_id');
        // });
        // Schema::table('expenses', function (Blueprint $table) {
        //     $table->foreignId('transaction_id')->constrained('transactions')->onDelete('cascade')->after('exp_category_id');
        //     $table->foreignId('transaction_element_id')->constrained('transaction_elements')->onDelete('cascade')->after('transaction_id');
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Schema::table('expenses', function (Blueprint $table) {
        //     $table->dropForeign(['transaction_id']);
        //     $table->dropForeign(['transaction_element_id']);
        // });
        // Schema::table('service_orders', function (Blueprint $table) {
        //     $table->dropForeign(['transaction_id']);
        //     $table->dropForeign(['transaction_element_id']);
        // });
        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->dropForeign(['closing_id']);
            $table->dropForeign(['transaction_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['patient_id']);
            $table->dropForeign(['service_id']);
            $table->dropForeign(['service_recestation_id']);
            $table->dropForeign(['doctor_id']);
            $table->dropForeign(['expense_id']);
            $table->dropForeign(['exp_voucher_id']);
        });
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['closing_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['patient_id']);
        });
        Schema::table('closings', function (Blueprint $table) {
            $table->dropForeign(['reception_id']);
            $table->dropForeign(['receptionist_id']);
        });
        Schema::dropIfExists('transaction_elements');
        Schema::dropIfExists('transactions');
        Schema::dropIfExists('closings');
        Schema::dropIfExists('receptions');
    }
};
