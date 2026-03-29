<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class EncryptPatientFields extends Command
{
    protected $signature = 'patients:encrypt-fields {--dry-run : Preview counts without making changes}';

    protected $description = 'Encrypt plain-text cnic, contact, and address fields on existing patient records';

    private function isAlreadyEncrypted(string $value): bool
    {
        try {
            Crypt::decryptString($value);

            return true;
        } catch (\Exception) {
            return false;
        }
    }

    public function handle(): int
    {
        $fields = ['cnic', 'contact', 'address'];
        $dryRun = $this->option('dry-run');

        if ($dryRun) {
            $this->info('DRY RUN — no changes will be made.');
        }

        foreach ($fields as $field) {
            $this->info("Processing field: {$field}");

            $records = DB::table('patients')
                ->whereNotNull($field)
                ->where($field, '!=', '')
                ->orderBy('id')
                ->select('id', $field)
                ->lazy(500);

            $updated = 0;
            $skipped = 0;

            foreach ($records as $row) {
                $raw = $row->$field;

                if ($this->isAlreadyEncrypted($raw)) {
                    $skipped++;

                    continue;
                }

                if (! $dryRun) {
                    DB::table('patients')
                        ->where('id', $row->id)
                        ->update([$field => Crypt::encryptString($raw)]);
                }

                $updated++;
            }

            $this->info("  Encrypted: {$updated} | Already encrypted (skipped): {$skipped}");
        }

        $this->info('Done.');

        return self::SUCCESS;
    }
}
