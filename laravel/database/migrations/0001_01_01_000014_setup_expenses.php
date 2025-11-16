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
        Schema::create('expense_categories', function (Blueprint $table) {
            $table->id();
            $table->integer('old_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('expense_vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('vc_number')->unique()->index();
            $table->integer('old_id')->nullable();
            $table->foreignId('exp_category_id')->constrained('expense_categories')->onDelete('cascade');

            $table->foreignId('service_order_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->foreignId('payed_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('payed_to_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->string('old_id')->nullable();
            $table->foreignId('voucher_id')->nullable()->constrained('expense_vouchers')->onDelete('cascade');
            $table->foreignId('exp_category_id')->nullable()->constrained('expense_categories')->onDelete('cascade');
            $table->foreignId('patient_id')->nullable()->constrained('patients')->onDelete('cascade');
            $table->foreignId('doctor_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->string('type')->default('CASH');
            $table->text('notes')->nullable();
            $table->json('notes_json')->nullable();

            $table->foreignId('service_order_id')->nullable()->constrained('users')->onDelete('cascade');

            $table->foreignId('payed_to')->nullable()->constrained('users')->onDelete('cascade');
            $table->string('payed_to_name')->nullable();
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('amount_alphabetical')->default('Zero');
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['voucher_id']);
            $table->dropForeign(['exp_category_id']);
            $table->dropForeign(['service_order_id']);
            $table->dropForeign(['payed_to']);
        });
        Schema::table('expense_vouchers', function (Blueprint $table) {
            $table->dropForeign(['exp_category_id']);
            $table->dropForeign(['service_order_id']);
            $table->dropForeign(['payed_to']);
        });
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('expense_vouchers');
        Schema::dropIfExists('expense_categories');
    }
};
