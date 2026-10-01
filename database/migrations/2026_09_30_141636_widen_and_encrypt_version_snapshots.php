<?php

use App\Services\VersionSnapshotEncryption;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * The snapshot columns were created as MySQL's native `json` type, but
     * be4a1b9 moved them to the SafeEncryptedJson cast — a native json
     * column rejects the non-JSON ciphertext ("Invalid JSON text"), so
     * every Patient/TreatmentRecord/ServiceOrder edit failed on MySQL.
     * Widen them to `longText` (as 2026_08_19_080405 did for
     * treatment_records).
     *
     * Only the column type changes here, and a table already converted (by
     * an earlier, interrupted run) is skipped, so this never blocks the
     * migrations after it for longer than the table copies themselves.
     * Encrypting the legacy plaintext snapshots is a separate, resumable
     * step: `php artisan versions:encrypt-snapshots`. The app reads both
     * plain and encrypted snapshots, so it works before that step finishes.
     */
    public function up(): void
    {
        foreach (VersionSnapshotEncryption::TABLES as $tableName) {
            if (Schema::getColumnType($tableName, 'snapshot') !== 'json') {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->longText('snapshot')->change();
            });
        }
    }

    /**
     * Decrypt snapshots back to plain JSON and restore the native `json`
     * columns. Every table is checked first; if any snapshot can't be turned
     * back into valid JSON (e.g. the APP_KEY changed), the rollback aborts
     * before anything is modified.
     *
     * Rolling back re-exposes PII/PHI in the audit trail and brings back the
     * "Invalid JSON text" failure on edits — use only in an emergency.
     */
    public function down(): void
    {
        $encryption = app(VersionSnapshotEncryption::class);

        foreach (VersionSnapshotEncryption::TABLES as $tableName) {
            try {
                $encryption->assertAllDecryptable($tableName);
            } catch (RuntimeException $e) {
                throw new RuntimeException('Cannot roll back: '.$e->getMessage().' Nothing was changed.', previous: $e);
            }
        }

        foreach (VersionSnapshotEncryption::TABLES as $tableName) {
            $encryption->decryptAll($tableName);

            Schema::table($tableName, function (Blueprint $table): void {
                $table->json('snapshot')->change();
            });
        }
    }
};
