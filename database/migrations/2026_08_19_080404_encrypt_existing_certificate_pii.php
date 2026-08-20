<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Birth/death/referral certificates store names, CNICs, and clinical
     * notes in plain string/text columns. Widens the previously-string
     * columns to text first (an encrypted payload runs longer than the
     * plaintext it replaces — same reason notes_json was widened in
     * 2026_03_29_094318_encrypt_existing_patient_and_service_order_data.php)
     * then bulk-encrypts any existing plaintext rows in place.
     */
    public function up(): void
    {
        Schema::table('birth_certificates', function (Blueprint $table): void {
            $table->text('child_name')->nullable()->change();
            $table->text('mother_name')->nullable()->change();
            $table->text('mother_cnic')->nullable()->change();
            $table->text('father_name')->nullable()->change();
            $table->text('father_cnic')->nullable()->change();
            $table->text('place_of_birth')->nullable()->change();
        });

        Schema::table('death_certificates', function (Blueprint $table): void {
            $table->text('place_of_death')->nullable()->change();
            $table->text('informant_name')->nullable()->change();
            $table->text('informant_relation')->nullable()->change();
            $table->text('informant_cnic')->nullable()->change();
        });

        DB::table('birth_certificates')
            ->select(['id', 'child_name', 'mother_name', 'mother_cnic', 'father_name', 'father_cnic', 'place_of_birth', 'remarks'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('birth_certificates')->where('id', $row->id)->update([
                        'child_name' => $this->encryptIfNeeded($row->child_name),
                        'mother_name' => $this->encryptIfNeeded($row->mother_name),
                        'mother_cnic' => $this->encryptIfNeeded($row->mother_cnic),
                        'father_name' => $this->encryptIfNeeded($row->father_name),
                        'father_cnic' => $this->encryptIfNeeded($row->father_cnic),
                        'place_of_birth' => $this->encryptIfNeeded($row->place_of_birth),
                        'remarks' => $this->encryptIfNeeded($row->remarks),
                    ]);
                }
            });

        DB::table('death_certificates')
            ->select(['id', 'place_of_death', 'antecedent_cause', 'informant_name', 'informant_relation', 'informant_cnic', 'remarks'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('death_certificates')->where('id', $row->id)->update([
                        'place_of_death' => $this->encryptIfNeeded($row->place_of_death),
                        'antecedent_cause' => $this->encryptIfNeeded($row->antecedent_cause),
                        'informant_name' => $this->encryptIfNeeded($row->informant_name),
                        'informant_relation' => $this->encryptIfNeeded($row->informant_relation),
                        'informant_cnic' => $this->encryptIfNeeded($row->informant_cnic),
                        'remarks' => $this->encryptIfNeeded($row->remarks),
                    ]);
                }
            });

        DB::table('referral_certificates')
            ->select(['id', 'notes'])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('referral_certificates')->where('id', $row->id)->update([
                        'notes' => $this->encryptIfNeeded($row->notes),
                    ]);
                }
            });
    }

    public function down(): void {}

    private function encryptIfNeeded(mixed $value): mixed
    {
        if ($value === null || $value === '') {
            return $value;
        }

        $value = (string) $value;

        try {
            Crypt::decryptString($value);

            return $value;
        } catch (Throwable) {
            return Crypt::encryptString($value);
        }
    }
};
