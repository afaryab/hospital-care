<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * activity_log grows unbounded (every create/update/delete across the
     * app logs a row) and is sorted/filtered by created_at, causer_id, and
     * event on every load of the admin Audit Logs table. expense_vouchers
     * is sorted by created_at on every load of its table. None of these
     * four columns currently have an index.
     */
    public function up(): void
    {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->index('created_at');
            $table->index('causer_id');
            $table->index('event');
        });

        Schema::table('expense_vouchers', function (Blueprint $table): void {
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::table('activity_log', function (Blueprint $table): void {
            $table->dropIndex(['created_at']);
            $table->dropIndex(['causer_id']);
            $table->dropIndex(['event']);
        });

        Schema::table('expense_vouchers', function (Blueprint $table): void {
            $table->dropIndex(['created_at']);
        });
    }
};
