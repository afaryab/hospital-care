<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Payment Methods
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->boolean('id_required')->default(false);
            $table->string('payables')->nullable()->comment('Morph class short name: bank_account, panel, or null');
            $table->timestamps();
        });

        // Bank Accounts
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('bank_name');
            $table->string('account_number')->nullable();
            $table->string('branch_code')->nullable();
            $table->string('iban')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Add payment method columns to transactions
        Schema::table('transactions', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('type')->constrained('payment_methods')->nullOnDelete();
            $table->nullableMorphs('payable');
            $table->string('reference_number')->nullable()->after('payable_id')->comment('Comma-separated reference numbers');
        });

        // Add payment method columns to transaction_elements
        Schema::table('transaction_elements', function (Blueprint $table) {
            $table->foreignId('payment_method_id')->nullable()->after('type')->constrained('payment_methods')->nullOnDelete();
            $table->nullableMorphs('payable');
            $table->string('reference_number')->nullable()->after('payable_id')->comment('Comma-separated reference numbers');
        });

        // Bank Transactions (uploaded bank statements)
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bank_account_id')->constrained('bank_accounts')->onDelete('cascade');
            $table->date('transaction_date');
            $table->text('description')->nullable();
            $table->decimal('debit', 15, 2)->nullable();
            $table->decimal('credit', 15, 2)->nullable();
            $table->decimal('balance', 15, 2)->nullable();
            $table->string('reference_number')->nullable();
            $table->foreignId('linked_transaction_id')->nullable()->constrained('transactions')->nullOnDelete();
            $table->timestamps();

            $table->unique(['bank_account_id', 'reference_number'], 'bank_transactions_unique_ref');
            $table->index(['bank_account_id', 'transaction_date']);
        });

        // Panel Cheques
        Schema::create('panel_cheques', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panel_id')->constrained('panels')->onDelete('cascade');
            $table->foreignId('bank_account_id')->nullable()->constrained('bank_accounts')->nullOnDelete();
            $table->string('cheque_number');
            $table->decimal('amount', 15, 2);
            $table->date('due_date')->nullable();
            $table->string('status')->default('pending')->comment('pending, received, bounced');
            $table->dateTime('received_at')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('linked_receaveable_id')->nullable()->constrained('receaveables')->nullOnDelete();
            $table->timestamps();

            $table->index(['panel_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('panel_cheques');
        Schema::dropIfExists('bank_transactions');

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropForeign(['payment_method_id']);
            $table->dropMorphs('payable');
            $table->dropColumn(['payment_method_id', 'reference_number']);
        });

        Schema::dropIfExists('bank_accounts');
        Schema::dropIfExists('payment_methods');
    }
};
