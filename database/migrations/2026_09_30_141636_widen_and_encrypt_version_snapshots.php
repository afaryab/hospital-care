<?php

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Version snapshot tables whose `snapshot` column is cast with
     * SafeEncryptedJson.
     *
     * @var list<string>
     */
    private array $tables = [
        'patient_versions',
        'treatment_record_versions',
        'service_order_versions',
    ];

    /**
     * The snapshot columns were created as MySQL's native `json` type, but
     * be4a1b9 moved them to the SafeEncryptedJson cast — a native json
     * column rejects the non-JSON ciphertext ("Invalid JSON text"), so
     * every Patient/TreatmentRecord/ServiceOrder edit failed on MySQL.
     * Widen them to `longText` (as 2026_08_19_080405 did for
     * treatment_records) and encrypt snapshots written before the cast
     * change, which still hold plaintext PII/PHI.
     *
     * Only values that are valid plain JSON are encrypted. Anything else is
     * already ciphertext (possibly under a key this app no longer holds)
     * and is left untouched, so nothing can be double-encrypted.
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->longText('snapshot')->change();
            });

            $this->eachSnapshot($tableName, function (string $snapshot): ?string {
                return json_validate($snapshot) ? Crypt::encryptString($snapshot) : null;
            });
        }
    }

    /**
     * Decrypt snapshots back to plain JSON and restore the native `json`
     * columns. Every row is checked first; if any snapshot can't be turned
     * back into valid JSON (e.g. the APP_KEY changed), the rollback aborts
     * before anything is modified.
     *
     * Rolling back re-exposes PII/PHI in the audit trail and brings back the
     * "Invalid JSON text" failure on edits — use only in an emergency.
     */
    public function down(): void
    {
        foreach ($this->tables as $tableName) {
            $this->eachSnapshot($tableName, function (string $snapshot, int $id) use ($tableName): null {
                if (! json_validate($this->decryptOrSelf($snapshot))) {
                    throw new RuntimeException("Cannot roll back: {$tableName}#{$id} snapshot does not decrypt to valid JSON. Nothing was changed.");
                }

                return null;
            });
        }

        foreach ($this->tables as $tableName) {
            $this->eachSnapshot($tableName, function (string $snapshot): ?string {
                $plain = $this->decryptOrSelf($snapshot);

                return $plain === $snapshot ? null : $plain;
            });

            Schema::table($tableName, function (Blueprint $table): void {
                $table->json('snapshot')->change();
            });
        }
    }

    /**
     * Walk every non-empty snapshot in a table; when the callback returns a
     * string, store it as the row's new snapshot.
     *
     * @param  callable(string, int): ?string  $transform
     */
    private function eachSnapshot(string $tableName, callable $transform): void
    {
        DB::table($tableName)
            ->select(['id', 'snapshot'])
            ->orderBy('id')
            ->chunkById(200, function ($rows) use ($tableName, $transform): void {
                foreach ($rows as $row) {
                    if ($row->snapshot === null || $row->snapshot === '') {
                        continue;
                    }

                    $updated = $transform((string) $row->snapshot, (int) $row->id);

                    if ($updated !== null) {
                        DB::table($tableName)->where('id', $row->id)->update(['snapshot' => $updated]);
                    }
                }
            });
    }

    private function decryptOrSelf(string $value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }
};
