<?php

namespace App\Services\Dms;

use App\Models\DmsFolder;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Materializes the "Patients" and "Doctors" system folder trees lazily —
 * only the first time an admin actually opens that branch of the explorer —
 * rather than provisioning a folder for every patient/doctor up front. Most
 * patients will never have a document uploaded against them; eagerly
 * creating thousands of empty folders would be pure waste.
 */
class DmsProvisioningService
{
    public const PATIENTS_ROOT_NAME = 'Patients';

    public const DOCTORS_ROOT_NAME = 'Doctors';

    public function patientsRoot(): DmsFolder
    {
        return $this->systemRoot(self::PATIENTS_ROOT_NAME);
    }

    public function doctorsRoot(): DmsFolder
    {
        return $this->systemRoot(self::DOCTORS_ROOT_NAME);
    }

    public function patientFolder(Patient $patient): DmsFolder
    {
        $root = $this->patientsRoot();

        return DB::transaction(function () use ($root, $patient) {
            return DmsFolder::query()->firstOrCreate(
                [
                    'parent_id' => $root->id,
                    'owner_type' => Patient::class,
                    'owner_id' => $patient->id,
                ],
                [
                    'name' => $patient->ps_number ?? "Patient #{$patient->id}",
                    'path' => $root->path.$root->id.'/',
                    'is_system' => true,
                    'created_by' => $this->systemUserId(),
                ]
            );
        });
    }

    public function doctorFolder(User $doctor): DmsFolder
    {
        $root = $this->doctorsRoot();

        return DB::transaction(function () use ($root, $doctor) {
            return DmsFolder::query()->firstOrCreate(
                [
                    'parent_id' => $root->id,
                    'owner_type' => User::class,
                    'owner_id' => $doctor->id,
                ],
                [
                    'name' => trim($doctor->name)." (#{$doctor->id})",
                    'path' => $root->path.$root->id.'/',
                    'is_system' => true,
                    'created_by' => $this->systemUserId(),
                ]
            );
        });
    }

    protected function systemRoot(string $name): DmsFolder
    {
        return DB::transaction(function () use ($name) {
            return DmsFolder::query()->firstOrCreate(
                ['parent_id' => null, 'name' => $name, 'is_system' => true],
                ['path' => '/', 'created_by' => $this->systemUserId()]
            );
        });
    }

    /**
     * The actor to attribute a lazily-provisioned system folder to. Prefers
     * the authenticated admin actually browsing the tree; falls back to a
     * find-or-create system user for contexts with no authenticated actor
     * (e.g. a queued job) — never a hardcoded id, which could point at a
     * different (or nonexistent) row depending on seeding order.
     */
    protected function systemUserId(): int
    {
        if ($userId = auth()->id()) {
            return (int) $userId;
        }

        return User::query()->firstOrCreate(
            ['email' => User::SYSTEM_SEEDER_EMAIL],
            ['name' => 'System', 'password' => Str::random(32)]
        )->id;
    }
}
