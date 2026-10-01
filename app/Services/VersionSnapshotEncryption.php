<?php

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Bulk encrypt/decrypt of the `snapshot` column on the version tables whose
 * models use the SafeEncryptedJson cast. Built for multi-million-row tables:
 * rows are walked by primary key and each batch is written in a single
 * transaction, so there is one commit per batch instead of one per row.
 * Every pass is idempotent and can be interrupted and re-run.
 */
class VersionSnapshotEncryption
{
    /**
     * @var list<string>
     */
    public const TABLES = [
        'patient_versions',
        'treatment_record_versions',
        'service_order_versions',
    ];

    /**
     * Encrypt snapshots that are still plain JSON. Values that aren't valid
     * JSON are already ciphertext (possibly under an older key) and are left
     * untouched, so nothing is ever double-encrypted.
     *
     * @param  callable(int $scanned, int $encrypted): void|null  $onBatch
     * @return array{scanned: int, encrypted: int}
     */
    public function encryptLegacy(string $table, int $batchSize = 1000, bool $dryRun = false, ?callable $onBatch = null): array
    {
        return $this->transform(
            $table,
            $batchSize,
            fn (string $snapshot): ?string => json_validate($snapshot) ? Crypt::encryptString($snapshot) : null,
            $dryRun,
            $onBatch,
        );
    }

    /**
     * Decrypt snapshots back to plain JSON.
     *
     * @return array{scanned: int, encrypted: int}
     */
    public function decryptAll(string $table, int $batchSize = 1000): array
    {
        return $this->transform($table, $batchSize, function (string $snapshot): ?string {
            $plain = $this->decryptOrSelf($snapshot);

            return $plain === $snapshot ? null : $plain;
        });
    }

    /**
     * Throw if any snapshot can't be turned back into valid JSON with the
     * current keys. Changes nothing.
     */
    public function assertAllDecryptable(string $table, int $batchSize = 1000): void
    {
        $this->transform($table, $batchSize, function (string $snapshot, int $id) use ($table): null {
            if (! json_validate($this->decryptOrSelf($snapshot))) {
                throw new RuntimeException("{$table}#{$id} snapshot does not decrypt to valid JSON.");
            }

            return null;
        });
    }

    /**
     * Walk every non-empty snapshot in id order; when the callback returns a
     * string, store it. Each batch is committed once.
     *
     * @param  callable(string, int): ?string  $transform
     * @param  callable(int, int): void|null  $onBatch
     * @return array{scanned: int, encrypted: int}
     */
    private function transform(string $table, int $batchSize, callable $transform, bool $dryRun = false, ?callable $onBatch = null): array
    {
        $scanned = 0;
        $changed = 0;

        DB::table($table)
            ->select(['id', 'snapshot'])
            ->orderBy('id')
            ->chunkById($batchSize, function ($rows) use ($table, $transform, $dryRun, $onBatch, &$scanned, &$changed): void {
                $updates = [];

                foreach ($rows as $row) {
                    $scanned++;

                    if ($row->snapshot === null || $row->snapshot === '') {
                        continue;
                    }

                    $updated = $transform((string) $row->snapshot, (int) $row->id);

                    if ($updated !== null) {
                        $updates[$row->id] = $updated;
                    }
                }

                $changed += count($updates);

                if ($updates !== [] && ! $dryRun) {
                    DB::transaction(function () use ($table, $updates): void {
                        foreach ($updates as $id => $snapshot) {
                            DB::table($table)->where('id', $id)->update(['snapshot' => $snapshot]);
                        }
                    });
                }

                if ($onBatch) {
                    $onBatch($scanned, $changed);
                }
            });

        return ['scanned' => $scanned, 'encrypted' => $changed];
    }

    private function decryptOrSelf(string $value): string
    {
        try {
            return Crypt::decryptString($value);
        } catch (DecryptException) {
            return $value;
        }
    }
}
