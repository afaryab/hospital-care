<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The Paeds M.O services used to be billed under OPD; 2026_09_26_194405
 * moved them to the new PED department. Receptions whose allowed_departments
 * list was set before then (e.g. ["OPD", ...]) lost the ability to bill them:
 * every Peds slip was rejected. Restore it by adding PED wherever OPD is
 * allowed. Receptions without a list (allowed everything) are untouched.
 */
return new class extends Migration
{
    private const MARKER = 'add_ped_to_receptions_allowing_opd';

    public function up(): void
    {
        $changed = [];

        foreach (DB::table('receptions')->select(['id', 'allowed_departments'])->get() as $reception) {
            $allowed = json_decode((string) $reception->allowed_departments, true);

            if (! is_array($allowed) || ! in_array('OPD', $allowed, true) || in_array('PED', $allowed, true)) {
                continue;
            }

            $allowed[] = 'PED';

            DB::table('receptions')->where('id', $reception->id)->update([
                'allowed_departments' => json_encode(array_values($allowed)),
                'updated_at' => now(),
            ]);

            $changed[] = $reception->id;
        }

        if ($changed !== []) {
            DB::table('activity_log')->insert([
                'log_name' => 'default',
                'description' => 'PED added to reception allowed departments (Paeds services moved from OPD)',
                'event' => self::MARKER,
                'properties' => json_encode(['reception_ids' => $changed]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Remove PED again, but only from the receptions this migration changed.
     * The activity-log entry stays: the audit trail is append-only.
     */
    public function down(): void
    {
        $entry = DB::table('activity_log')->where('event', self::MARKER)->latest('id')->first();
        $receptionIds = json_decode((string) $entry?->properties, true)['reception_ids'] ?? [];

        foreach (DB::table('receptions')->whereIn('id', $receptionIds)->get(['id', 'allowed_departments']) as $reception) {
            $allowed = json_decode((string) $reception->allowed_departments, true);

            if (! is_array($allowed)) {
                continue;
            }

            DB::table('receptions')->where('id', $reception->id)->update([
                'allowed_departments' => json_encode(array_values(array_diff($allowed, ['PED']))),
                'updated_at' => now(),
            ]);
        }

    }
};
