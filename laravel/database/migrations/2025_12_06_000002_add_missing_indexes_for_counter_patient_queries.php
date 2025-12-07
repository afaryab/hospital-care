<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // service_orders: frequent filter by patient_id + type
        if (Schema::hasTable('service_orders')) {
            Schema::table('service_orders', function (Blueprint $table) {
                if (!self::hasIndexOnColumns('service_orders', ['patient_id'])) {
                    $table->index('patient_id');
                }
                if (!self::hasIndexOnColumns('service_orders', ['type'])) {
                    $table->index('type');
                }
                if (!self::hasIndexOnColumns('service_orders', ['patient_id','type'])) {
                    $table->index(['patient_id','type'], 'service_orders_patient_type_index');
                }
                if (!self::hasIndexOnColumns('service_orders', ['service_id'])) {
                    $table->index('service_id');
                }
            });
        }

        // services: filter by service_department_id
        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                if (!self::hasIndexOnColumns('services', ['service_department_id'])) {
                    $table->index('service_department_id');
                }
            });
        }

        // service_recestations: filter by service_department_id
        if (Schema::hasTable('service_recestations')) {
            Schema::table('service_recestations', function (Blueprint $table) {
                if (!self::hasIndexOnColumns('service_recestations', ['service_department_id'])) {
                    $table->index('service_department_id');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('service_orders')) {
            Schema::table('service_orders', function (Blueprint $table) {
                try { $table->dropIndex('service_orders_patient_type_index'); } catch (\Throwable $e) {}
                try { $table->dropIndex('service_orders_patient_id_index'); } catch (\Throwable $e) {}
                try { $table->dropIndex('service_orders_type_index'); } catch (\Throwable $e) {}
                try { $table->dropIndex('service_orders_service_id_index'); } catch (\Throwable $e) {}
            });
        }

        if (Schema::hasTable('services')) {
            Schema::table('services', function (Blueprint $table) {
                try { $table->dropIndex('services_service_department_id_index'); } catch (\Throwable $e) {}
            });
        }

        if (Schema::hasTable('service_recestations')) {
            Schema::table('service_recestations', function (Blueprint $table) {
                try { $table->dropIndex('service_recestations_service_department_id_index'); } catch (\Throwable $e) {}
            });
        }
    }

    private static function hasIndex(string $table, string $indexName): bool
    {
        try {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            return array_key_exists($indexName, $indexes);
        } catch (\Throwable $e) {
            return false;
        }
    }

    private static function hasIndexOnColumns(string $table, array $columns): bool
    {
        try {
            $schemaManager = Schema::getConnection()->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            $target = array_map('strtolower', $columns);
            sort($target);
            foreach ($indexes as $index) {
                $idxCols = array_map('strtolower', $index->getColumns());
                sort($idxCols);
                if ($idxCols === $target) {
                    return true;
                }
            }
        } catch (\Throwable $e) {
            // If DBAL not available, be conservative and skip (assume exists)
            return true;
        }
        return false;
    }
};
