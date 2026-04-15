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
        Schema::table('receptions', function (Blueprint $table) {
            $table->dropColumn(['is_cash_allowed', 'is_cheques_allowed', 'is_card_allowed']);
        });

        Schema::table('receptionists', function (Blueprint $table) {
            $table->foreignId('reception_id')->nullable()->after('user_id')->constrained('receptions')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('receptionists', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reception_id');
        });

        Schema::table('receptions', function (Blueprint $table) {
            $table->tinyInteger('is_cash_allowed')->default(1)->after('is_allowed_to_pay_from_petty_cash');
            $table->tinyInteger('is_cheques_allowed')->default(0)->after('is_cash_allowed');
            $table->tinyInteger('is_card_allowed')->default(0)->after('is_cheques_allowed');
        });
    }
};
