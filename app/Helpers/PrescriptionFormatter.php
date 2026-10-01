<?php

namespace App\Helpers;

use App\Models\Drug;
use Illuminate\Support\Str;

/**
 * Turns prescription rows into the plain-language dose instructions printed
 * on patient-facing documents. The frequency → pattern mapping lives in
 * config/prescriptions.php so no view hard-codes it.
 */
class PrescriptionFormatter
{
    /** @var array<string, string|null> */
    private static array $drugForms = [];

    /**
     * "1 Tablet", "2 Capsule", … — the per-dose quantity and unit.
     *
     * @param  array<string, mixed>  $rx
     */
    public static function quantity(array $rx): string
    {
        $unit = self::unit($rx);
        $count = preg_match('/^\s*(\d+(?:\.\d+)?)\s*(tab|cap|sachet|tsp|drop|puff)/i', (string) ($rx['dose'] ?? ''), $m) ? $m[1] : '1';

        return $unit ? "{$count} {$unit}" : (string) ($rx['dose'] ?? '');
    }

    /**
     * "1 + 1 + 1" (Morning + Noon + Night), or a label such as "4 times/day".
     */
    public static function pattern(?string $frequency): string
    {
        $entry = self::frequency($frequency);

        if ($entry === null) {
            return trim((string) $frequency);
        }

        return $entry['pattern'] !== null
            ? implode(' + ', $entry['pattern'])
            : (string) ($entry['label'] ?? $frequency);
    }

    /**
     * Plain-English timing, e.g. "Morning, Noon, Night".
     */
    public static function when(?string $frequency): string
    {
        return (string) (self::frequency($frequency)['when'] ?? '');
    }

    /**
     * @return array{pattern: array<int, int>|null, when: string, label?: string}|null
     */
    public static function frequency(?string $frequency): ?array
    {
        $key = strtoupper(preg_replace('/[^A-Za-z]/', '', (string) $frequency));

        return config("prescriptions.frequencies.{$key}");
    }

    /**
     * @param  array<string, mixed>  $rx
     */
    private static function unit(array $rx): ?string
    {
        $form = $rx['form'] ?? null;

        if (blank($form) && filled($rx['drug_name'] ?? null)) {
            $name = (string) $rx['drug_name'];
            $form = self::$drugForms[$name] ??= Drug::query()->where('name', $name)->value('type');
        }

        if (blank($form)) {
            return null;
        }

        $forms = config('prescriptions.forms');
        $key = Str::lower(trim((string) $form));

        return $forms[$key] ?? $forms[rtrim($key, 's')] ?? Str::title($form);
    }
}
