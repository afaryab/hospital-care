<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * TreatmentRecord's narrative fields (chief_complaint, diagnosis_text,
     * etc.) and JSON fields (examination_findings, prescriptions,
     * department_specific_data, dental_chart) hold clinical PHI in plain
     * text. The JSON columns are widened from MySQL's native `json` type
     * to `longText` first — a native json column rejects the non-JSON
     * ciphertext an encrypted value becomes, the same reason
     * service_orders.notes_json was widened in
     * 2026_03_29_094318_encrypt_existing_patient_and_service_order_data.php.
     * referral_to is widened the same way — it was declared `string`
     * (VARCHAR 255) when the table was created, and Crypt::encryptString()'s
     * output (base64 IV + ciphertext + MAC, JSON-wrapped) regularly exceeds
     * 255 bytes even for a short plaintext value.
     */
    public function up(): void
    {
        Schema::table('treatment_records', function (Blueprint $table): void {
            $table->longText('examination_findings')->nullable()->change();
            $table->longText('prescriptions')->nullable()->change();
            $table->longText('department_specific_data')->nullable()->change();
            $table->longText('dental_chart')->nullable()->change();
            $table->text('referral_to')->nullable()->change();
        });

        DB::table('treatment_records')
            ->select([
                'id', 'chief_complaint', 'history_of_present_illness', 'diagnosis_text',
                'treatment_plan', 'outcome_notes', 'referral_to',
                'examination_findings', 'prescriptions', 'department_specific_data', 'dental_chart',
            ])
            ->orderBy('id')
            ->chunkById(200, function ($rows): void {
                foreach ($rows as $row) {
                    DB::table('treatment_records')->where('id', $row->id)->update([
                        'chief_complaint' => $this->encryptIfNeeded($row->chief_complaint),
                        'history_of_present_illness' => $this->encryptIfNeeded($row->history_of_present_illness),
                        'diagnosis_text' => $this->encryptIfNeeded($row->diagnosis_text),
                        'treatment_plan' => $this->encryptIfNeeded($row->treatment_plan),
                        'outcome_notes' => $this->encryptIfNeeded($row->outcome_notes),
                        'referral_to' => $this->encryptIfNeeded($row->referral_to),
                        'examination_findings' => $this->encryptIfNeeded($row->examination_findings),
                        'prescriptions' => $this->encryptIfNeeded($row->prescriptions),
                        'department_specific_data' => $this->encryptIfNeeded($row->department_specific_data),
                        'dental_chart' => $this->encryptIfNeeded($row->dental_chart),
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
