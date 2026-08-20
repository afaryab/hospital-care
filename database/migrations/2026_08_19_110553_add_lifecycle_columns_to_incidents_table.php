<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Extends the incident table (until now just an automated
     * BreachDetectionService write target) with the columns needed for a
     * full classify → assign → investigate → resolve → close lifecycle
     * and manual reporting (PHC guideline §9).
     */
    public function up(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->foreignId('department_id')->nullable()->after('patient_id')->constrained('service_departments')->nullOnDelete();
            $table->foreignId('reported_by')->nullable()->after('department_id')->constrained('users')->nullOnDelete();
            $table->foreignId('assigned_to')->nullable()->after('reported_by')->constrained('users')->nullOnDelete();
            $table->dateTime('classified_at')->nullable()->after('occurred_at');
            $table->dateTime('assigned_at')->nullable()->after('classified_at');
            $table->dateTime('investigated_at')->nullable()->after('assigned_at');
            $table->text('investigation_notes')->nullable()->after('investigated_at');
            $table->dateTime('resolved_at')->nullable()->after('investigation_notes');
            $table->text('resolution_notes')->nullable()->after('resolved_at');
            $table->dateTime('closed_at')->nullable()->after('resolution_notes');
            $table->foreignId('closed_by')->nullable()->after('closed_at')->constrained('users')->nullOnDelete();
        });

        // The default/only status ever written until now was 'open' — remap
        // to the new lifecycle's initial stage before changing the column
        // default, so no existing row is left holding a value the new
        // IncidentStatus enum doesn't recognize.
        DB::table('incidents')->where('status', 'open')->update(['status' => 'reported']);

        Schema::table('incidents', function (Blueprint $table): void {
            $table->string('status')->default('reported')->change();
        });
    }

    public function down(): void
    {
        Schema::table('incidents', function (Blueprint $table): void {
            $table->string('status')->default('open')->change();
        });

        DB::table('incidents')->where('status', 'reported')->update(['status' => 'open']);

        Schema::table('incidents', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('closed_by');
            $table->dropColumn(['classified_at', 'assigned_at', 'investigated_at', 'investigation_notes', 'resolved_at', 'resolution_notes', 'closed_at']);
            $table->dropConstrainedForeignId('assigned_to');
            $table->dropConstrainedForeignId('reported_by');
            $table->dropConstrainedForeignId('department_id');
        });
    }
};
