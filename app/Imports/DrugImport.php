<?php

namespace App\Imports;

use App\Models\Drug;
use App\Models\DrugCategory;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

/**
 * Imports drugs from CSV / Excel.
 *
 * Expected columns (case-insensitive, spaces → underscores):
 *   name, generic_name, type, category, strength, manufacturer,
 *   default_dose, default_frequency, default_duration, default_route,
 *   usage_instructions, contraindications, side_effects
 *
 * Rows are upserted on (name + generic_name) — safe to re-import.
 */
class DrugImport implements SkipsEmptyRows, SkipsOnError, ToCollection, WithChunkReading, WithHeadingRow
{
    use Importable, SkipsErrors;

    private array $categoryCache = [];

    public int $imported = 0;

    public int $skipped = 0;

    public function collection(Collection $rows): void
    {
        foreach ($rows as $row) {
            $name = trim((string) ($row['name'] ?? ''));
            if (empty($name)) {
                $this->skipped++;

                continue;
            }

            $categoryId = $this->resolveCategory($row['category'] ?? null);

            Drug::withoutEvents(function () use ($row, $name, $categoryId): void {
                Drug::updateOrCreate(
                    [
                        'name' => $name,
                        'generic_name' => trim((string) ($row['generic_name'] ?? $row['salt'] ?? '')),
                    ],
                    [
                        'type' => $this->clean($row['type'] ?? null),
                        'drug_category_id' => $categoryId,
                        'strength' => $this->clean($row['strength'] ?? null),
                        'manufacturer' => $this->clean($row['manufacturer'] ?? null),
                        'default_dose' => $this->clean($row['default_dose'] ?? $row['dose'] ?? null),
                        'default_frequency' => $this->clean($row['default_frequency'] ?? $row['frequency'] ?? null),
                        'default_duration' => $this->clean($row['default_duration'] ?? $row['duration'] ?? null),
                        'default_route' => $this->clean($row['default_route'] ?? $row['route'] ?? null),
                        'usage_instructions' => $this->clean($row['usage_instructions'] ?? $row['usage'] ?? null),
                        'contraindications' => $this->clean($row['contraindications'] ?? null),
                        'side_effects' => $this->clean($row['side_effects'] ?? null),
                        'is_active' => true,
                    ]
                );
            });

            $this->imported++;
        }
    }

    public function chunkSize(): int
    {
        return 200;
    }

    private function resolveCategory(mixed $name): ?int
    {
        if (empty($name)) {
            return null;
        }

        $trimmed = trim((string) $name);
        $key = Str::lower($trimmed);

        if (! isset($this->categoryCache[$key])) {
            $cat = DrugCategory::whereRaw('LOWER(name) = ?', [$key])->first()
                ?? DrugCategory::create(['name' => $trimmed]);
            $this->categoryCache[$key] = $cat->id;
        }

        return $this->categoryCache[$key];
    }

    private function clean(mixed $value): ?string
    {
        $s = trim((string) ($value ?? ''));

        return $s === '' ? null : $s;
    }
}
