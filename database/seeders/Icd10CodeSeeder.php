<?php

namespace Database\Seeders;

use App\Services\Icd10\ClaMlIcd10Importer;
use Illuminate\Database\Seeder;
use RuntimeException;

class Icd10CodeSeeder extends Seeder
{
    /**
     * Runs on every `migrate --seed` (including every container start — see
     * docker/cli/start.sh), so this must stay cheap and idempotent. The
     * importer upserts by code, so re-running it just re-syncs descriptions
     * and categories rather than duplicating rows.
     */
    public function run(ClaMlIcd10Importer $importer): void
    {
        $path = database_path('data/icd10/claml-2019.xml');

        try {
            $importer->import($path);
        } catch (RuntimeException $exception) {
            $this->command?->warn("Skipping ICD-10 import: {$exception->getMessage()}");
        }
    }
}
