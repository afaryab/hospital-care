<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->foreignId('appointment_id')->nullable()->after('doctor_id')->constrained('appointments')->onDelete('set null');
            // Pins Priority-mode appointment reservations at the top of the queue
            // ordering (ORDER BY priority DESC, created_at ASC) without touching
            // created_at / the audit trail.
            $table->tinyInteger('priority')->default(0)->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('service_orders', function (Blueprint $table) {
            $table->dropConstrainedForeignId('appointment_id');
            $table->dropColumn('priority');
        });
    }
};
