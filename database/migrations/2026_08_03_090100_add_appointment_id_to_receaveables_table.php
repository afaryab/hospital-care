<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('receaveables', function (Blueprint $table) {
            // Draft receivables created when a Priority-mode appointment is booked
            // have no transaction yet, so they link back through the appointment
            // instead of transaction_id.
            $table->foreignId('appointment_id')->nullable()->after('panel_id')->constrained('appointments')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('receaveables', function (Blueprint $table) {
            $table->dropConstrainedForeignId('appointment_id');
        });
    }
};
