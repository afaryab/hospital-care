<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * BirthCertificate already has an is_locked finalization lock
     * (2026_08_10_174559_create_birth_certificates_table.php). Extends the
     * same pattern to DeathCertificate and ReferralCertificate, which had
     * no immutability enforcement at all — either could be silently edited
     * after issuance.
     */
    public function up(): void
    {
        Schema::table('death_certificates', function (Blueprint $table): void {
            $table->boolean('is_locked')->default(false)->after('remarks');
            $table->dateTime('locked_at')->nullable()->after('is_locked');
            $table->foreignId('locked_by')->nullable()->after('locked_at')->constrained('users')->nullOnDelete();
        });

        Schema::table('referral_certificates', function (Blueprint $table): void {
            $table->boolean('is_locked')->default(false)->after('notes');
            $table->dateTime('locked_at')->nullable()->after('is_locked');
            $table->foreignId('locked_by')->nullable()->after('locked_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('referral_certificates', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('locked_by');
            $table->dropColumn(['is_locked', 'locked_at']);
        });

        Schema::table('death_certificates', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('locked_by');
            $table->dropColumn(['is_locked', 'locked_at']);
        });
    }
};
