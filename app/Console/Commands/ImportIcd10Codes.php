<?php

namespace App\Console\Commands;

use App\Services\Icd10\ClaMlIcd10Importer;
use Illuminate\Console\Command;
use RuntimeException;

class ImportIcd10Codes extends Command
{
    protected $signature = 'icd10:import
                            {path? : Path to a WHO ClaML ICD-10 XML file, defaults to the bundled 2019 revision}';

    protected $description = 'Import (or re-sync) ICD-10 codes from a WHO ClaML XML file into icd10_codes';

    public function handle(ClaMlIcd10Importer $importer): int
    {
        $path = $this->argument('path') ?? database_path('data/icd10/claml-2019.xml');

        $this->info("Importing ICD-10 codes from [{$path}]...");

        try {
            $stats = $importer->import($path);
        } catch (RuntimeException $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info(sprintf(
            'Parsed %d chapters, %d blocks, %d categories. Imported %d codes (%d skipped for missing description).',
            $stats['chapters'],
            $stats['blocks'],
            $stats['categories'],
            $stats['imported'],
            $stats['skipped'],
        ));

        return self::SUCCESS;
    }
}
