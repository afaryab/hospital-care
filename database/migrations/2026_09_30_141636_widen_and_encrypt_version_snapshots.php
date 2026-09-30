<?php

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
     */
    public function up(): void
    {
        foreach ($this->tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table): void {
                $table->longText('snapshot')->change();
            });

            DB::table($tableName)
                ->select(['id', 'snapshot'])
                ->orderBy('id')
                ->chunkById(200, function ($rows) use ($tableName): void {
                    foreach ($rows as $row) {
                        $encrypted = $this->encryptIfNeeded($row->snapshot);

                        if ($encrypted !== $row->snapshot) {
                            DB::table($tableName)->where('id', $row->id)->update(['snapshot' => $encrypted]);
                        }
                    }
                });
        }
    }

    /**
     * Not reversible: encrypted snapshots cannot be stored back in a
     * native json column.
     */
    public function down(): void {}

    private function encryptIfNeeded(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = (string) $value;

        try {
            Crypt::decryptString($value);

            return $value;
        } catch (Throwable) {
            return Crypt::encryptString($value);
        }
    }
};
