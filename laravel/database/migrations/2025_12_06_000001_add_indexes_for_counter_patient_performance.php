<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Add index on patients.ps_number for fast lookups by PS/…
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (!self::hasIndex('patients', 'patients_ps_number_index')) {
                    $table->index('ps_number');
                }
            });
        }

        // Add composite index on closings(status, receptionist_id) used in open counter fetches
        if (Schema::hasTable('closings')) {
            Schema::table('closings', function (Blueprint $table) {
                if (!self::hasIndex('closings', 'closings_status_receptionist_id_index')) {
                    $table->index(['status', 'receptionist_id']);
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('patients')) {
            Schema::table('patients', function (Blueprint $table) {
                if (self::hasIndex('patients', 'patients_ps_number_index')) {
                    $table->dropIndex('patients_ps_number_index');
                }
            });
        }

        if (Schema::hasTable('closings')) {
            Schema::table('closings', function (Blueprint $table) {
                if (self::hasIndex('closings', 'closings_status_receptionist_id_index')) {
                    $table->dropIndex('closings_status_receptionist_id_index');
                }
            });
        }
    }

    private static function hasIndex(string $table, string $indexName): bool
    {
        // Portable index detection: use Doctrine DBAL if available; fallback false to avoid duplicate creation
        try {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            return array_key_exists($indexName, $indexes);
        } catch (\Throwable $e) {
            // If DBAL not installed, assume missing to create, Laravel will guard duplicates per driver
            return false;
        }
    }
};
