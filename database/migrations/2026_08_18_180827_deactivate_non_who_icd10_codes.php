<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Deactivates codes from the 2026_05_16 hand-picked seed that turned out
     * not to be valid WHO ICD-10 (cross-checked against the full WHO ClaML
     * import added alongside this migration). Two kinds:
     *
     * - Redundant ICD-10-CM padding, where the correct WHO code is already
     *   imported under a shorter form (e.g. K40.90 -> WHO's K40.9).
     * - CM-only codes with no clean single WHO equivalent at all (e.g.
     *   R73.09, which doesn't correspond to either of WHO's R73.0/R73.9).
     *
     * Deliberately NOT included here: codes that looked "missing" from a
     * naive cross-check but are actually valid WHO codes the importer
     * doesn't yet expand (WHO defines E10-E14's, F10-F19's, and K25-K28's
     * fourth-character subdivisions via a shared <Modifier> block rather
     * than individual <Class> elements — see ClaMlIcd10Importer), plus
     * A90/A91 (Dengue) and G89, which are absent from the bundled WHO XML
     * revision entirely rather than being non-standard. Deactivating those
     * would remove real, commonly-used diagnoses; left active pending a
     * follow-up to the importer and/or a more complete WHO source file.
     *
     * Disabled rather than deleted so history (any treatment_records
     * already pointing at one) isn't disturbed, and the codes simply
     * disappear from the active picker going forward.
     */
    public function up(): void
    {
        DB::table('icd10_codes')
            ->whereIn('code', $this->codes())
            ->update(['is_active' => false, 'updated_at' => now()]);
    }

    public function down(): void
    {
        DB::table('icd10_codes')
            ->whereIn('code', $this->codes())
            ->update(['is_active' => true, 'updated_at' => now()]);
    }

    /** @return list<string> */
    private function codes(): array
    {
        return [
            // Redundant ICD-10-CM padding — WHO equivalent already imported
            // one digit shorter.
            'I25.10', // -> I25.1
            'K40.90', // -> K40.9
            'K57.30', // -> K57.3
            'K80.20', // -> K80.2
            'N30.00', // -> N30.0
            'S62.00', // -> S62.0
            'S72.00', // -> S72.0
            'S82.00', // -> S82.0
            'Z00.00', // -> Z00.0
            'Z34.00', // -> Z34.0
            'Z34.90', // -> Z34.9
            'Z38.00', // -> Z38.0

            // CM-only codes with no clean single WHO equivalent.
            'E11.65', // WHO's E10-E14 modifier only defines .0-.9, no .65
            'R07.9',  // WHO's nearest is R07.4 (chest pain, unspecified)
            'R10.9',  // WHO's nearest is R10.4 (other/unspecified abdominal pain)
            'R53.1',  // WHO's R53 has no subdivisions at all
            'R73.09', // WHO has R73.0 and R73.9 as distinct codes, no R73.09
            'Z00.01', // WHO's Z00 has no "with abnormal findings" subdivision
        ];
    }
};
