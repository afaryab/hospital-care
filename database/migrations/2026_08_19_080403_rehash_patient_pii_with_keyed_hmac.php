<?php

use App\Helpers\PiiHasher;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * patients.cnic_hash/contact_hash were unsalted SHA-256 over the
     * normalized value — trivially rebuildable offline from a database dump
     * (CNIC/phone number spaces are small enough to brute-force). Rehashes
     * every existing row with a keyed HMAC (PiiHasher, keyed off APP_KEY)
     * so the blind index can no longer be reconstructed without the key.
     */
    public function up(): void
    {
        DB::table('patients')
            ->select(['id', 'cnic', 'contact'])
            ->where(fn ($q) => $q->whereNotNull('cnic')->orWhereNotNull('contact'))
            ->orderBy('id')
            ->chunkById(200, function ($patients): void {
                foreach ($patients as $patient) {
                    $plainCnic = $this->decrypt($patient->cnic);
                    $plainContact = $this->decrypt($patient->contact);

                    DB::table('patients')->where('id', $patient->id)->update([
                        'cnic_hash' => $plainCnic !== null && $plainCnic !== '' ? PiiHasher::cnic($plainCnic) : null,
                        'contact_hash' => $plainContact !== null && $plainContact !== '' ? PiiHasher::contact($plainContact) : null,
                    ]);
                }
            });
    }

    public function down(): void
    {
        // Not reversible — the previous unsalted SHA-256 index is being
        // retired intentionally and should not be recreated.
    }

    private function decrypt(?string $value): ?string
    {
        if ($value === null || $value === '') {
            return $value;
        }

        try {
            return Crypt::decryptString($value);
        } catch (Throwable) {
            return $value;
        }
    }
};
