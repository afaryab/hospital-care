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
        DB::table('patients')
            ->select(['id', 'cnic', 'contact', 'address'])
            ->orderBy('id')
            ->chunkById(200, function ($patients): void {
                foreach ($patients as $patient) {
                    $plainCnic = $this->extractPlainValue($patient->cnic);

                    DB::table('patients')
                        ->where('id', $patient->id)
                        ->update([
                            'cnic' => $this->encryptIfNeeded($patient->cnic),
                            'contact' => $this->encryptIfNeeded($patient->contact),
                            'address' => $this->encryptIfNeeded($patient->address),
                            'cnic_hash' => $plainCnic !== null && $plainCnic !== ''
                                ? hash('sha256', strtoupper(trim($plainCnic)))
                                : null,
                        ]);
                }
            });

        Schema::table('service_orders', function (Blueprint $table): void {
            $table->longText('notes_json')->nullable()->change();
        });

        DB::table('service_orders')
            ->select(['id', 'notes_json'])
            ->orderBy('id')
            ->chunkById(200, function ($serviceOrders): void {
                foreach ($serviceOrders as $serviceOrder) {
                    DB::table('service_orders')
                        ->where('id', $serviceOrder->id)
                        ->update([
                            'notes_json' => $this->encryptIfNeeded($serviceOrder->notes_json),
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
        } catch (\Throwable) {
            return Crypt::encryptString($value);
        }
    }

    private function extractPlainValue(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        $value = (string) $value;

        try {
            return Crypt::decryptString($value);
        } catch (\Throwable) {
            return $value;
        }
    }
};
