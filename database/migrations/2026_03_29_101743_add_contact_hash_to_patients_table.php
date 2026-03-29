<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->string('contact_hash', 64)->nullable()->index()->after('contact');
        });

        DB::table('patients')
            ->select(['id', 'contact'])
            ->orderBy('id')
            ->chunkById(200, function ($patients): void {
                foreach ($patients as $patient) {
                    $contact = $patient->contact;

                    if ($contact === null || $contact === '') {
                        continue;
                    }

                    $plainContact = (string) $contact;

                    try {
                        $plainContact = Crypt::decryptString($plainContact);
                    } catch (\Throwable) {
                    }

                    $normalized = preg_replace('/\D+/', '', $plainContact) ?: null;

                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([
                            'contact_hash' => $normalized ? hash('sha256', $normalized) : null,
                        ]);
                }
            });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            $table->dropColumn('contact_hash');
        });
    }
};
