<?php

namespace App\Console\Commands;

use App\Enum\CounterStatus;
use App\Enum\ServiceOrderStatus;
use App\Models\Administrator;
use App\Models\Closing;
use App\Models\Dentist;
use App\Models\EmergencyDoctor;
use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\IndDoctor;
use App\Models\MigrationLog;
use App\Models\OpdDoctor;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Receptionist;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\ServiceRecestation;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\UltrasoundDoctor;
use App\Models\UpgradeProcess;
use App\Models\User;
use App\Models\XrayTechnician;
use App\Services\AbacusClosingService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Processton\Abacus\Models\AbacusIncoming;
use Processton\Abacus\Models\AbacusTransaction;

class SyncOldHIMS extends Command
{
    protected $signature = 'app:sync-old-hims
        {--entity= : Specific entity to sync (users,services,receptions,panels,patients,expense-categories,by-closings,closings,transactions,vouchers,treatments,abacus-closings,recent-transactions,all)}
        {--reset : Reset sync cursors for the specified entity}
        {--batch-size=2000 : Number of records per batch}
        {--dry-run : Preview what would be synced without making changes}
        {--since= : ISO date — only used by recent-transactions (default: 1 month ago)}';

    protected $description = 'Incremental data sync from old HIMS database (hospital_care_analytics) to new schema';

    // In-memory caches for frequently accessed data
    protected array $userCache = [];

    protected array $patientCache = [];

    protected array $serviceCache = [];

    protected array $serviceRecestationCache = [];

    protected array $serviceDeptCache = [];

    protected array $receptionCache = [];

    /** @var array<int, int> Maps old reception_counters.id → new merged Reception.id */
    protected array $oldReceptionIdMap = [];

    protected array $closingCache = [];

    protected array $expenseCategoryCache = [];

    protected array $panelCache = [];

    // In-memory sequence counters — keyed by date prefix (Y/m or Y/m/d).
    // Initialized lazily (once per distinct key) to avoid COUNT queries per record.
    protected array $ctCounters = [];

    protected array $trCounters = [];

    protected array $vcCounters = [];

    protected array $psCounters = [];

    protected string $batchId;

    protected bool $dryRun = false;

    protected int $batchSize;

    // Entity sync order — dependencies first
    // Vouchers run before by-closings so that VOUCHER-PAY transactions always find a
    // pre-migrated voucher via old_id; on-demand creation in enrichExpenseTransaction()
    // is just a fallback for any edge cases.
    // 'by-closings' replaces the old separate closings/transactions/abacus-closings passes:
    // it processes one closing at a time, migrating its transactions (with on-demand patient
    // creation) and Abacus entries before moving to the next closing.
    protected array $entityOrder = [
        'receptions',
        'users',
        'services',
        'panels',
        'expense-categories',
        'vouchers',
        'by-closings',
        'treatments',
    ];

    public function info($string, $verbosity = null): void
    {
        parent::info($string, $verbosity);
        Log::info("[SyncOldHIMS] {$string}");
    }

    public function handle(): int
    {
        if (env('ENABLE_OLD_SYNC', false) !== 'hims') {
            $this->error('ENABLE_OLD_SYNC is not set to "hims". Set ENABLE_OLD_SYNC=hims in .env to proceed.');

            return 1;
        }

        ini_set('max_execution_time', 7200);
        set_time_limit(0);
        ini_set('memory_limit', '2G');

        $this->dryRun = (bool) $this->option('dry-run');
        $this->batchSize = (int) $this->option('batch-size');
        $this->batchId = 'sync_'.now()->format('Y_m_d_H_i_s').'_'.uniqid();

        // Verify secondary DB connection
        try {
            DB::connection('secondary')->getPdo();
            DB::connection('secondary')->getPdo()->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        } catch (\Exception $e) {
            $this->error('Secondary database connection failed: '.$e->getMessage());

            return 1;
        }

        $this->info("Starting sync batch: {$this->batchId}".($this->dryRun ? ' [DRY RUN]' : ''));

        // Determine which entities to sync
        $entity = $this->option('entity') ?? 'all';

        if ($this->option('reset')) {
            $this->resetCursors($entity);

            return 0;
        }

        // Preload reference data caches
        $this->preloadCaches();

        // All entities that syncEntity() can handle (superset of entityOrder)
        $allEntities = array_unique(array_merge($this->entityOrder, [
            'patients', 'closings', 'transactions', 'abacus-closings', 'recent-transactions',
        ]));

        if ($entity === 'all') {
            foreach ($this->entityOrder as $e) {
                $this->syncEntity($e);
            }
        } else {
            $entities = explode(',', $entity);
            foreach ($entities as $e) {
                $e = trim($e);
                if (! in_array($e, $allEntities)) {
                    $this->error("Unknown entity: {$e}. Valid: ".implode(', ', $allEntities));

                    return 1;
                }
                $this->syncEntity($e);
            }
        }

        $this->printSyncSummary();

        return 0;
    }

    // =========================================================================
    // Cursor Management — tracks last synced record per entity
    // =========================================================================

    protected function getCursor(string $entity): int
    {
        return (int) UpgradeProcess::where('name', "sync_cursor_{$entity}")->value('value') ?? 0;
    }

    protected function setCursor(string $entity, int $value): void
    {
        UpgradeProcess::updateOrCreate(
            ['name' => "sync_cursor_{$entity}"],
            ['value' => $value]
        );
    }

    protected function resetCursors(string $entity): void
    {
        if ($entity === 'all') {
            $deleted = UpgradeProcess::where('name', 'like', 'sync_cursor_%')->delete();
            $this->info("Reset all sync cursors ({$deleted} entries removed)");
        } else {
            $entities = explode(',', $entity);
            foreach ($entities as $e) {
                $e = trim($e);
                $deleted = UpgradeProcess::where('name', 'like', "sync_cursor_{$e}%")->delete();
                $this->info("Reset cursor for: {$e} ({$deleted} entries removed)");
            }
        }
    }

    // =========================================================================
    // Cache Preloading
    // =========================================================================

    protected function preloadCaches(): void
    {
        $this->info('Preloading caches...');

        User::all(['id', 'email', 'name'])->each(function ($user) {
            $this->userCache[$user->id] = $user;
        });

        ServiceDepartment::all()->each(function ($dept) {
            $this->serviceDeptCache[$dept->slug] = $dept;
        });

        Reception::all(['id', 'name'])->each(function ($r) {
            $this->receptionCache[$r->id] = $r;
        });

        // Rebuild old→new reception ID map from old DB
        try {
            DB::connection('secondary')
                ->table('reception_counters')
                ->select('id', 'counter_name')
                ->orderBy('id')
                ->each(function ($old) {
                    $match = Reception::where('name', $old->counter_name)->first();
                    if ($match) {
                        $this->oldReceptionIdMap[$old->id] = $match->id;
                    }
                });
        } catch (\Exception $e) {
            $this->warn('Could not rebuild old reception ID map: '.$e->getMessage());
        }

        Panel::all(['id', 'name', 'code'])->each(function ($p) {
            $this->panelCache[$p->id] = $p;
        });

        ExpenseCategory::all(['id', 'old_id', 'name'])->each(function ($c) {
            if ($c->old_id) {
                $this->expenseCategoryCache[$c->old_id] = $c;
            }
        });

        $this->info('Caches loaded: '.count($this->userCache).' users, '.count($this->serviceDeptCache).' depts, '.count($this->receptionCache).' receptions, '.count($this->panelCache).' panels');
    }

    // =========================================================================
    // Entity Sync Router
    // =========================================================================

    protected function syncEntity(string $entity): void
    {
        $this->newLine();
        $this->info("═══ Syncing: {$entity} ═══");

        $startTime = microtime(true);

        match ($entity) {
            'users' => $this->syncUsers(),
            'services' => $this->syncServices(),
            'receptions' => $this->syncReceptions(),
            'panels' => $this->syncPanels(),
            'patients' => $this->syncPatients(),
            'expense-categories' => $this->syncExpenseCategories(),
            'by-closings' => $this->syncByClosings(),
            'closings' => $this->syncClosings(),
            'transactions' => $this->syncTransactions(),
            'vouchers' => $this->syncVouchers(),
            'treatments' => $this->syncTreatments(),
            'abacus-closings' => $this->syncAbacusClosings(),
            'recent-transactions' => $this->syncRecentTransactions(),
        };

        $elapsed = round(microtime(true) - $startTime, 2);
        $this->info("Completed {$entity} in {$elapsed}s");
    }

    // =========================================================================
    // 1. Users
    // =========================================================================

    protected function syncUsers(): void
    {
        $cursor = $this->getCursor('users');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('aauth_users');

        DB::connection('secondary')
            ->table('aauth_users')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($users) use (&$cursor, &$processed, &$skipped) {
                foreach ($users as $user) {
                    if (User::where('id', $user->id)->exists()) {
                        $cursor = $user->id;
                        $skipped++;

                        continue;
                    }

                    if (empty($user->email) || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                        MigrationLog::logSkipped('users', 'aauth_users', $user->id, 'Invalid email', (array) $user);
                        $cursor = $user->id;
                        $skipped++;

                        continue;
                    }

                    if (empty($user->name)) {
                        MigrationLog::logSkipped('users', 'aauth_users', $user->id, 'Empty name', (array) $user);
                        $cursor = $user->id;
                        $skipped++;

                        continue;
                    }

                    // Check for existing user with same email
                    if (User::where('email', $user->email)->exists()) {
                        MigrationLog::logSkipped('users', 'aauth_users', $user->id, 'Duplicate email: '.$user->email);
                        $cursor = $user->id;
                        $skipped++;

                        continue;
                    }

                    if (! $this->dryRun) {
                        try {
                            User::insertOrIgnore([
                                'id' => $user->id,
                                'name' => $user->name,
                                'email' => $user->email,
                                'password' => Hash::make('password'),
                                'password_expired_at' => Carbon::now(),
                                'is_active' => $user->banned == 0,
                                'banned_message' => $user->banned_message,
                                'last_login' => $user->last_login,
                                'last_activity' => $user->last_activity,
                                'last_login_attempt' => $user->last_login_attempt,
                                'ip_address' => $user->ip_address,
                                'login_attempts' => $user->login_attempts ?? 0,
                                'profile_img_path' => $user->profile_img_path ? ltrim($user->profile_img_path, 'public/') : null,
                                'profile_img_id' => $user->profile_img_id,
                                'created_at' => $user->created_on,
                                'updated_at' => $user->modified_on ?? now(),
                            ]);

                            // Insert role profiles
                            $this->insertUserProfiles($user);

                            $this->userCache[$user->id] = (object) ['id' => $user->id, 'email' => $user->email, 'name' => $user->name];

                        } catch (\Exception $e) {
                            MigrationLog::logError('users', 'aauth_users', $user->id, $e->getMessage());
                            $cursor = $user->id;

                            continue;
                        }
                    }

                    $cursor = $user->id;
                    $processed++;
                }

                if (! $this->dryRun) {
                    $this->setCursor('users', $cursor);
                }
                $this->info("Users batch: +{$processed} synced, {$skipped} skipped (cursor: {$cursor})");
            });

        $this->setCursor('users', $cursor);
        $this->syncDone('users', $processed, $skipped, $total);
    }

    protected function insertUserProfiles(object $user): void
    {
        $profileMap = [
            'is_super_admin' => [Administrator::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
            'is_opd_doctor' => [OpdDoctor::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
            'is_inpatient_doctor' => [IndDoctor::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
            'is_emergency_doctor' => [EmergencyDoctor::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
            'is_dentist' => [Dentist::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
            'is_ultrasound_doc' => [UltrasoundDoctor::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
            'is_xray_tech' => [XrayTechnician::class, ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on ?? now()]],
        ];

        foreach ($profileMap as $flag => $config) {
            if (isset($user->{$flag}) && $user->{$flag}) {
                [$model, $data] = $config;
                $model::insertOrIgnore($data);
            }
        }

        // For receptionists: create one profile per reception (allow all)
        if (isset($user->is_receptionist) && $user->is_receptionist) {
            $receptionIds = Reception::pluck('id');
            foreach ($receptionIds as $receptionId) {
                Receptionist::insertOrIgnore([
                    'user_id' => $user->id,
                    'reception_id' => $receptionId,
                    'created_at' => $user->created_on,
                    'updated_at' => $user->modified_on ?? now(),
                ]);
            }
        }
    }

    // =========================================================================
    // 2. Services — match to seeded departments by slug
    // =========================================================================

    protected function syncServices(): void
    {
        $cursor = $this->getCursor('services');
        $migrated = 0;
        $skipped = 0;
        $total = 0;

        $serviceTypes = [
            ['key' => 'OPD', 'table' => 'opd_services'],
            ['key' => 'IND', 'table' => 'inpd_services', 'recestation_table' => 'recestation_services'],
            ['key' => 'EMG', 'table' => 'emergency_services'],
            ['key' => 'DNT', 'table' => 'dental_services'],
            ['key' => 'PTH', 'table' => 'test_services'],
            ['key' => 'ULT', 'table' => 'ultrasound_services'],
            ['key' => 'XRAY', 'table' => 'xray_services'],
        ];

        foreach ($serviceTypes as $st) {
            $total += $this->oldCount($st['table']);

            $department = $this->serviceDeptCache[$st['key']] ?? null;
            if (! $department) {
                $this->warn("Service department {$st['key']} not found in seeded data — skipping.");

                continue;
            }

            // Sync main services
            $lastSynced = $this->getCursor("services_{$st['key']}");

            DB::connection('secondary')
                ->table($st['table'])
                ->where('id', '>', $lastSynced)
                ->orderBy('id')
                ->chunk($this->batchSize, function ($services) use ($department, $st, &$lastSynced, &$migrated, &$skipped) {
                    foreach ($services as $service) {
                        // Match by old_id or by name+department
                        $exists = Service::where('old_id', $service->id)
                            ->where('service_department_id', $department->id)
                            ->exists();

                        if (! $exists) {
                            // Also try matching by slug/name to seeded data
                            $exists = Service::where('slug', $service->post_key)
                                ->where('service_department_id', $department->id)
                                ->exists();

                            if ($exists) {
                                // Link seeded service to old_id
                                if (! $this->dryRun) {
                                    Service::where('slug', $service->post_key)
                                        ->where('service_department_id', $department->id)
                                        ->update(['old_id' => $service->id]);
                                }
                                $skipped++;
                            }
                        } else {
                            $skipped++;
                        }

                        if (! $exists && ! $this->dryRun) {
                            $serviceProviderTypes = [];
                            if (isset($service->is_doctor_selectable) && $service->is_doctor_selectable) {
                                $serviceProviderTypes = match ($st['key']) {
                                    'OPD' => [OpdDoctor::class],
                                    'IND' => [IndDoctor::class],
                                    'DNT' => [Dentist::class],
                                    default => [],
                                };
                            }

                            Service::insertOrIgnore([
                                'name' => $service->name,
                                'slug' => $service->post_key,
                                'service_department_id' => $department->id,
                                'charges' => $service->charges,
                                'charges_include_tax' => $service->charges_including_tax ?? 0,
                                'tax_rate' => $service->tax_rate ?? 0,
                                'have_service_provider' => in_array($st['key'], ['OPD', 'IND']) && ($service->is_doctor_selectable ?? false),
                                'is_composit_service' => $st['key'] === 'IND',
                                'service_provider_types' => json_encode($serviceProviderTypes),
                                'generate_service_order' => 1,
                                'created_by' => $service->entered_by ?? 1,
                                'old_id' => $service->id,
                                'created_at' => $service->created_on,
                                'updated_at' => now(),
                            ]);
                            $migrated++;
                        }

                        $lastSynced = $service->id;
                    }

                    $this->setCursor("services_{$st['key']}", $lastSynced);
                    $this->info("Synced {$st['key']} services batch (cursor: {$lastSynced})");
                });

            // Sync recestation services if applicable
            if (isset($st['recestation_table'])) {
                $lastRecest = $this->getCursor("services_{$st['key']}_recestation");

                DB::connection('secondary')
                    ->table($st['recestation_table'])
                    ->where('id', '>', $lastRecest)
                    ->orderBy('id')
                    ->chunk($this->batchSize, function ($services) use ($department, &$lastRecest) {
                        foreach ($services as $service) {
                            if (! ServiceRecestation::where('old_id', $service->id)->where('service_department_id', $department->id)->exists()) {
                                if (! $this->dryRun) {
                                    ServiceRecestation::insertOrIgnore([
                                        'name' => $service->name,
                                        'slug' => ($service->post_key == 0) ? null : $service->post_key,
                                        'service_department_id' => $department->id,
                                        'charges' => $service->charges,
                                        'charges_include_tax' => $service->charges_including_tax ?? 0,
                                        'tax_rate' => $service->tax_rate ?? 0,
                                        'created_by' => $service->entered_by ?? 1,
                                        'old_id' => $service->id,
                                        'created_at' => $service->created_on,
                                        'updated_at' => now(),
                                    ]);
                                }
                            }
                            $lastRecest = $service->id;
                        }
                        $this->setCursor("services_{$department->slug}_recestation", $lastRecest);
                    });
            }
        }

        // Rebuild service caches
        Service::all(['id', 'old_id', 'service_department_id', 'slug'])->each(function ($s) {
            if ($s->old_id) {
                $dept = ServiceDepartment::find($s->service_department_id);
                if ($dept) {
                    $this->serviceCache["{$dept->slug}_{$s->old_id}"] = $s;
                }
            }
        });

        ServiceRecestation::all(['id', 'old_id', 'service_department_id'])->each(function ($s) {
            if ($s->old_id) {
                $this->serviceRecestationCache[$s->old_id] = $s;
            }
        });

        $this->syncDone('services', $migrated, $skipped, $total);
    }

    // =========================================================================
    // 3. Receptions
    // =========================================================================

    protected function syncReceptions(): void
    {
        $cursor = $this->getCursor('receptions');
        $merged = 0;
        $created = 0;
        $total = $this->oldCount('reception_counters');

        DB::connection('secondary')
            ->table('reception_counters')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($receptions) use (&$cursor, &$merged, &$created) {
                foreach ($receptions as $reception) {
                    if (! $this->dryRun) {
                        // Merge by name: find existing reception with same name
                        $existing = Reception::where('name', $reception->counter_name)->first();

                        if ($existing) {
                            // Map old ID → existing merged reception ID
                            $this->oldReceptionIdMap[$reception->id] = $existing->id;
                            $this->receptionCache[$existing->id] = $existing;
                            $merged++;
                        } else {
                            $allowedDepts = [];
                            if ($reception->is_opd_allowed) {
                                $allowedDepts[] = 'OPD';
                            }
                            if ($reception->is_inpatient_allowed) {
                                $allowedDepts[] = 'IND';
                            }
                            if ($reception->is_emergency_allowed) {
                                $allowedDepts[] = 'EMG';
                            }
                            $allowedDepts = array_merge($allowedDepts, ['DNT', 'PTH', 'ULT', 'XRAY']);

                            $newReception = Reception::create([
                                'name' => $reception->counter_name,
                                'allowed_departments' => $allowedDepts,
                                'is_allowed_to_pay_voucher' => $reception->is_allowed_to_pay_voucher,
                                'is_allowed_to_pay_from_petty_cash' => $reception->is_allowed_to_pay_from_petty_cash,
                            ]);

                            $this->oldReceptionIdMap[$reception->id] = $newReception->id;
                            $this->receptionCache[$newReception->id] = $newReception;
                            $created++;
                        }
                    }
                    $cursor = $reception->id;
                }
                $this->setCursor('receptions', $cursor);
            });

        $this->syncDone('receptions', $created, $merged, $total);
    }

    // =========================================================================
    // 4. Panels (new — not in FetchOldHIMS)
    // =========================================================================

    protected function syncPanels(): void
    {
        $cursor = $this->getCursor('panels');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('panel_companies', ['is_deleted' => 0]);

        DB::connection('secondary')
            ->table('panel_companies')
            ->where('id', '>', $cursor)
            ->where('is_deleted', 0)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($panels) use (&$cursor, &$processed, &$skipped) {
                foreach ($panels as $panel) {
                    if (Panel::where('id', $panel->id)->exists()) {
                        $skipped++;
                    } elseif (! $this->dryRun) {
                        Panel::insertOrIgnore([
                            'id' => $panel->id,
                            'name' => $panel->name,
                            'code' => Str::upper(Str::slug($panel->name, '-')),
                            'is_active' => true,
                            'created_at' => $panel->created_on,
                            'updated_at' => $panel->modified_on ?? now(),
                        ]);

                        $this->panelCache[$panel->id] = (object) ['id' => $panel->id, 'name' => $panel->name];
                        $processed++;
                    }
                    $cursor = $panel->id;
                }
                $this->setCursor('panels', $cursor);
            });

        $this->syncDone('panels', $processed, $skipped, $total);
    }

    // =========================================================================
    // 5. Patients — with encryption via SafeEncrypted cast
    // =========================================================================

    protected function syncPatients(): void
    {
        $cursor = $this->getCursor('patients');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('patients');

        DB::connection('secondary')
            ->table('patients')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($patients) use (&$cursor, &$processed, &$skipped) {
                foreach ($patients as $patient) {
                    if (Patient::where('id', $patient->id)->exists()) {
                        $cursor = $patient->id;
                        $skipped++;

                        continue;
                    }

                    if (! $this->dryRun) {
                        try {
                            $createdAt = Carbon::parse($patient->created_on);
                            $psNumber = $this->generatePsNumber($createdAt);

                            $contact = $this->validatePhoneNumber($patient->patient_contact_mobile)
                                ? $this->formatPhoneNumber($patient->patient_contact_mobile)
                                : null;

                            $cnic = $this->validateCnic($patient->patient_cnic)
                                ? $this->formatCnic($patient->patient_cnic)
                                : null;

                            // Decode BLOB address
                            $address = is_string($patient->patient_address) ? $patient->patient_address : null;

                            // Use the Patient model to create so SafeEncrypted cast encrypts cnic/contact/address
                            $newPatient = new Patient;
                            $newPatient->id = $patient->id;
                            $newPatient->ps_number = $psNumber;
                            $newPatient->name = $patient->pateint_name;
                            $newPatient->gender = $patient->gender;
                            $newPatient->age_group = $patient->age_group ?: null;
                            $newPatient->age_days = $patient->age_days ?: null;
                            $newPatient->age_dob = $this->sanitizeDate($patient->age_dob);
                            $newPatient->address = $address;
                            $newPatient->guardian = $patient->guardian;
                            $newPatient->relation = $patient->relation;
                            $newPatient->contact = $contact;
                            $newPatient->cnic = $cnic;
                            $newPatient->created_at = $patient->created_on;
                            $newPatient->updated_at = $patient->modified_on ?? now();

                            // Use saveQuietly to skip observer auto-numbering
                            $newPatient->saveQuietly();

                            $this->patientCache[$patient->id] = (object) ['id' => $patient->id, 'name' => $patient->pateint_name];
                            $processed++;

                        } catch (\Exception $e) {
                            MigrationLog::logError('patients', 'patients', $patient->id, $e->getMessage());
                            $this->warn("Patient {$patient->id} error: ".$e->getMessage());
                        }
                    }

                    $cursor = $patient->id;
                }

                if (! $this->dryRun) {
                    $this->setCursor('patients', $cursor);
                }
                $this->info("Patients batch: +{$processed} synced, {$skipped} skipped (cursor: {$cursor})");
            });

        $this->setCursor('patients', $cursor);
        $this->syncDone('patients', $processed, $skipped, $total);
    }

    // =========================================================================
    // 6. Expense Categories
    // =========================================================================

    protected function syncExpenseCategories(): void
    {
        $cursor = $this->getCursor('expense-categories');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('expenses_categories');

        DB::connection('secondary')
            ->table('expenses_categories')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($categories) use (&$cursor, &$processed, &$skipped) {
                foreach ($categories as $cat) {
                    // Match by old_id or by name to seeded data
                    $existing = ExpenseCategory::where('old_id', $cat->id)->first();
                    if (! $existing) {
                        $existing = ExpenseCategory::where('name', $cat->name)->first();
                        if ($existing && ! $this->dryRun) {
                            // Link seeded category to old_id
                            $existing->update(['old_id' => $cat->id]);
                            $this->expenseCategoryCache[$cat->id] = $existing;
                            $processed++;
                        }
                    } else {
                        $skipped++;
                    }

                    if (! $existing && ! $this->dryRun) {
                        $newCat = ExpenseCategory::create([
                            'old_id' => $cat->id,
                            'name' => $cat->name,
                            'type' => $cat->type ?? null,
                            'pay_doc' => $cat->pay_doc ?? false,
                            'pay_others' => $cat->pay_others ?? false,
                            'pay_users' => $cat->pay_users ?? false,
                            'pay_patient' => false,
                        ]);
                        $this->expenseCategoryCache[$cat->id] = $newCat;
                        $processed++;
                    }

                    $cursor = $cat->id;
                }
                $this->setCursor('expense-categories', $cursor);
            });

        $this->syncDone('expense-categories', $processed, $skipped, $total);
    }

    // =========================================================================
    // 7. By-Closings — closing-by-closing migration (replaces separate
    //    closings / transactions / abacus-closings passes)
    // =========================================================================

    protected function syncByClosings(): void
    {
        $cursor = $this->getCursor('by-closings');
        $processedClosings = 0;
        $skippedClosings = 0;
        $processedTransactions = 0;
        $totalClosings = $this->oldCount('reception_counters_closings');
        $totalTransactions = $this->oldCount('reception_counters_closings_transactions');
        $service = new AbacusClosingService;

        DB::connection('secondary')
            ->table('reception_counters_closings')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->each(function ($oldClosing) use (&$cursor, &$processedClosings, &$processedTransactions, $service, &$skippedClosings) {
                // 1. Migrate the closing record
                $newClosing = $this->migrateClosing($oldClosing);

                $trCount = 0;

                // 2. Migrate every transaction that belongs to this closing
                DB::connection('secondary')
                    ->table('reception_counters_closings_transactions')
                    ->where('counter_id', $oldClosing->id)
                    ->orderBy('id')
                    ->each(function ($tr) use ($newClosing, &$trCount) {
                        if (Transaction::where('old_id', $tr->id)->exists()) {
                            return;
                        }

                        if (! $this->dryRun) {
                            try {
                                $this->migrateTransaction($tr, $newClosing?->id);
                                $trCount++;
                            } catch (\Exception $e) {
                                MigrationLog::logError('transactions', 'reception_counters_closings_transactions', $tr->id, $e->getMessage());
                                $this->warn("Transaction {$tr->id} error: ".$e->getMessage());
                            }
                        }
                    });

                // 3. Create Abacus double-entry for this closing
                if ($newClosing && ! $this->dryRun) {
                    try {
                        $service->createEntriesForClosing($newClosing);
                    } catch (\Exception $e) {
                        MigrationLog::logError('abacus-closings', 'closings', $newClosing->id, $e->getMessage());
                        $this->warn("Abacus closing {$newClosing->id} error: ".$e->getMessage());
                    }
                }

                if ($newClosing) {
                    $processedClosings++;
                } else {
                    $skippedClosings++;
                }
                $processedTransactions += $trCount;

                $cursor = $oldClosing->id;
                $this->setCursor('by-closings', $cursor);

                $label = $newClosing ? $newClosing->ct_number : 'skipped';
                $this->info("Closing {$oldClosing->id} [{$label}]: {$trCount} transactions (cursor: {$cursor})");
            });

        $this->syncDone('closings', $processedClosings, $skippedClosings, $totalClosings);
        $this->syncDone('transactions', $processedTransactions, 0, $totalTransactions);
    }

    // =========================================================================
    // 8. Closings (standalone pass — kept for --entity=closings backward compat)
    // =========================================================================

    protected function syncClosings(): void
    {
        $cursor = $this->getCursor('closings');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('reception_counters_closings');

        DB::connection('secondary')
            ->table('reception_counters_closings')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($closings) use (&$cursor, &$processed, &$skipped) {
                foreach ($closings as $closing) {
                    $alreadyExists = Closing::where('old_id', $closing->id)->exists();
                    $new = $this->migrateClosing($closing);
                    if ($alreadyExists) {
                        $skipped++;
                    } elseif ($new) {
                        $processed++;
                    }
                    $cursor = $closing->id;
                }

                $this->setCursor('closings', $cursor);
                $this->info("Closings batch: +{$processed} (cursor: {$cursor})");
            });

        $this->setCursor('closings', $cursor);
        $this->syncDone('closings', $processed, $skipped, $total);
    }

    protected function migrateClosing(object $closing): ?Closing
    {
        // Return existing record if already migrated
        $existing = Closing::where('old_id', $closing->id)->orWhere('id', $closing->id)->first();
        if ($existing) {
            $this->closingCache[$closing->id] = $existing;

            return $existing;
        }

        if ($this->dryRun) {
            return null;
        }

        try {
            // Use closing date (cash received) for CT number — a counter opened Jan 31
            // and closed Feb 2 belongs to February, not January.
            $closingDate = Carbon::parse($closing->cash_recieving_time ?? $closing->created_on);
            $ctNumber = $this->generateCtNumber($closingDate);

            $receptionId = $this->oldReceptionIdMap[$closing->reception_id] ?? ($this->receptionCache[$closing->reception_id]->id ?? null);
            $userId = $this->userCache[$closing->user_id]->id ?? null;

            if (! $receptionId || ! $userId) {
                MigrationLog::logSkipped('closings', 'reception_counters_closings', $closing->id,
                    'Missing reception_id ('.$closing->reception_id.') or user_id ('.$closing->user_id.')');

                return null;
            }

            $newClosing = new Closing;
            $newClosing->id = $closing->id;
            $newClosing->old_id = $closing->id;
            $newClosing->reception_id = $receptionId;
            $newClosing->receptionist_id = $userId;
            $newClosing->ct_number = $ctNumber;
            $newClosing->status = $closing->status === 'CLOSED' ? CounterStatus::REPORTED : CounterStatus::OPEN;
            $newClosing->opening_amount = $closing->opening_amount ?? 0;
            $newClosing->closing_amount = $closing->closing_amount ?? 0;
            $newClosing->closing_amount_cash = $closing->closing_amount_cash ?? 0;
            $newClosing->closing_amount_cheque = 0;
            $newClosing->closing_amount_card = ($closing->closing_amount_card ?? 0) + ($closing->closing_amount_creditcard ?? 0);
            $newClosing->expense_payed = $closing->expense_payed ?? 0;
            $newClosing->amount_received = $closing->closing_amount ?? 0;
            $newClosing->closed_at = $closing->cash_recieving_time;
            $newClosing->cash_recieving_time = $closing->cash_recieving_time;
            $newClosing->reported_by = $userId;
            $newClosing->created_at = $closing->created_on;
            $newClosing->updated_at = $closing->modified_on ?? now();
            $newClosing->saveQuietly();

            $this->closingCache[$closing->id] = $newClosing;

            return $newClosing;
        } catch (\Exception $e) {
            MigrationLog::logError('closings', 'reception_counters_closings', $closing->id, $e->getMessage());
            $this->warn("Closing {$closing->id} error: ".$e->getMessage());

            return null;
        }
    }

    // =========================================================================
    // 8. Transactions + Transaction Elements
    // =========================================================================

    protected function syncTransactions(): void
    {
        $cursor = $this->getCursor('transactions');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('reception_counters_closings_transactions');

        DB::connection('secondary')
            ->table('reception_counters_closings_transactions')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($transactions) use (&$cursor, &$processed, &$skipped) {
                foreach ($transactions as $tr) {
                    if (Transaction::where('old_id', $tr->id)->exists()) {
                        $skipped++;
                        $cursor = $tr->id;

                        continue;
                    }

                    if (! $this->dryRun) {
                        try {
                            $this->migrateTransaction($tr);
                            $processed++;
                        } catch (\Exception $e) {
                            MigrationLog::logError('transactions', 'reception_counters_closings_transactions', $tr->id, $e->getMessage());
                            $this->warn("Transaction {$tr->id} error: ".$e->getMessage());
                        }
                    }

                    $cursor = $tr->id;
                }

                $this->setCursor('transactions', $cursor);
                $this->info("Transactions batch: +{$processed} (cursor: {$cursor})");
            });

        $this->setCursor('transactions', $cursor);
        $this->syncDone('transactions', $processed, $skipped, $total);
    }

    protected function migrateTransaction(object $tr, ?int $knownClosingId = null): void
    {
        $isExpense = $tr->income_or_expence !== 'INCOME';
        $createdAt = Carbon::parse($tr->created_on);

        // Generate TR number
        $trNumber = $this->generateTrNumber($createdAt);

        // Use the pre-resolved closing ID when available (by-closings flow), otherwise look it up
        $closingId = $knownClosingId ?? $this->resolveClosingId($tr->counter_id);
        $userId = $this->userCache[$tr->user_id]->id ?? null;
        $patientId = $this->resolvePatientId($tr->patient_id);

        if (! $userId) {
            MigrationLog::logSkipped('transactions', 'reception_counters_closings_transactions', $tr->id,
                'Missing user_id: '.$tr->user_id);

            return;
        }

        $transactionData = [
            'old_id' => $tr->id,
            'tr_number' => $trNumber,
            'closing_id' => $closingId,
            'created_by' => $userId,
            'patient_id' => $patientId,
            'type' => $isExpense ? 'EXP' : $this->mapPaymentType($tr->type),
            'income_or_expense' => $isExpense ? 'EXPENSE' : 'INCOME',
            'amount' => $this->sanitizeAmount($tr->amount, $isExpense),
            'orignal_amount' => $this->sanitizeAmount($tr->orignal_amount, $isExpense),
            'customer_payed' => $isExpense ? 0 : $this->sanitizeAmount($tr->customer_payed),
            'change' => $isExpense ? 0 : $this->sanitizeAmount($tr->change),
            'edited_amount' => $this->sanitizeAmount($tr->edited_amount, $isExpense),
            'created_at' => $tr->created_on,
            'updated_at' => $tr->modified_on ?? now(),
        ];

        // Handle expense specifics
        if ($isExpense) {
            $this->enrichExpenseTransaction($tr, $transactionData);
        }

        $newTrId = Transaction::insertGetId($transactionData);

        // Migrate elements for this transaction
        $elements = DB::connection('secondary')
            ->table('reception_counters_closings_transaction_elements')
            ->where('closing_transaction_id', $tr->id)
            ->get();

        foreach ($elements as $element) {
            $this->migrateTransactionElement($element, $tr, $newTrId, $closingId, $transactionData);
        }
    }

    protected function migrateTransactionElement(object $element, object $tr, int $newTrId, ?int $closingId, array $transactionData): void
    {
        if (! $element->type || ! $element->id) {
            return;
        }

        $isExpenseElement = in_array($element->type, ['EXP', 'VOUCHER-PAY', 'INPT-EXP']);

        $baseData = [
            'old_id' => $element->id,
            'closing_id' => $closingId,
            'transaction_id' => $newTrId,
            'created_by' => $this->userCache[$tr->user_id]->id ?? 1,
            'amount' => $this->sanitizeAmount($element->amount, $isExpenseElement),
            'orignal_amount' => $this->sanitizeAmount($element->original_amount, $isExpenseElement),
            'customer_payed' => 0,
            'change' => 0,
            'income_or_expense' => $tr->income_or_expence === 'INCOME' ? 'INCOME' : 'EXPENSE',
            'created_at' => $element->created_on,
            'updated_at' => $element->modified_on ?? now(),
        ];

        // Map element type to new schema type and resolve foreign keys
        $elementSpecificData = match ($element->type) {
            'OPD', 'EMER', 'DENTAL', 'ULTRA', 'XRAY', 'LAB' => [
                'type' => $this->mapElementType($element->type),
                'doctor_id' => $this->userCache[$element->doctor_id]->id ?? null,
                'patient_id' => $this->resolvePatientId($tr->patient_id),
                'service_id' => $this->resolveServiceId($element->service_id, $this->mapElementType($element->type)),
            ],
            'INPT' => [
                'type' => 'IND',
                'doctor_id' => $this->userCache[$element->doctor_id]->id ?? null,
                'patient_id' => $this->resolvePatientId($tr->patient_id),
                'service_id' => $this->resolveServiceId($element->service_id, 'IND'),
            ],
            'RECES' => [
                'type' => 'RECES-IND',
                'patient_id' => $this->resolvePatientId($tr->patient_id),
                'service_recestation_id' => $this->serviceRecestationCache[$element->service_id]->id ?? null,
            ],
            'EXP', 'VOUCHER-PAY', 'INPT-EXP' => [
                'type' => $element->type === 'VOUCHER-PAY' ? 'VOUCHER_PAY' : ($element->type === 'INPT-EXP' ? 'IND_EXP' : 'PETTY_CASH'),
                'expense_category_id' => $transactionData['expense_category_id'] ?? null,
                'exp_voucher_id' => $transactionData['exp_voucher_id'] ?? null,
                'notes' => $transactionData['notes'] ?? null,
            ],
            default => null,
        };

        if ($elementSpecificData === null) {
            MigrationLog::logSkipped('transaction_elements', 'reception_counters_closings_transaction_elements',
                $element->id, "Unknown element type: {$element->type}");

            return;
        }

        $data = array_merge($baseData, $elementSpecificData);

        // Use createQuietly to avoid observer auto-creating service orders during migration
        $te = TransactionElement::createQuietly($data);

        // For VOUCHER_PAY elements, backfill the ExpenseVoucher with transaction references
        if ($te->type === 'VOUCHER_PAY' && $te->exp_voucher_id) {
            ExpenseVoucher::where('id', $te->exp_voucher_id)
                ->update([
                    'transaction_id' => $te->transaction_id,
                    'transaction_element_id' => $te->id,
                ]);
        }

        // For income transactions with department_transaction_id, create service order
        if ($tr->income_or_expence === 'INCOME' && $element->department_transaction_id && in_array($element->type, ['INPT'])) {
            $this->createServiceOrderFromInpatientFile($element, $tr, $te);
        }

        // For RECES elements, bind to the parent IND service order via recestation_transactions
        if ($element->type === 'RECES' && $element->department_transaction_id) {
            $this->bindRecesToServiceOrder($element, $te);
        }
    }

    protected function createServiceOrderFromInpatientFile(object $element, object $tr, TransactionElement $te): void
    {
        $inpFile = DB::connection('secondary')
            ->table('inpatient_file')
            ->where('id', $element->department_transaction_id)
            ->first();

        if (! $inpFile) {
            return;
        }

        $soShort = 'IND/'.str_pad($inpFile->id, 8, '0', STR_PAD_LEFT);

        // Check if SO already exists for this inpatient file
        $existingSo = ServiceOrder::where('so_short', $soShort)->first();
        if ($existingSo) {
            $te->service_order_id = $existingSo->id;
            $te->saveQuietly();

            return;
        }

        $soCount = ServiceOrder::where('type', 'IND')->count() + 1;
        $soNumber = $te->patient_id
            ? (Patient::find($te->patient_id)?->ps_number ?? 'PS/0000/00/0000').'/IND/'.str_pad($soCount, 8, '0', STR_PAD_LEFT)
            : 'SO/'.Carbon::parse($element->created_on)->format('Ymd').'/'.str_pad($soCount, 4, '0', STR_PAD_LEFT);

        $so = ServiceOrder::createQuietly([
            'type' => 'IND',
            'status' => $this->mapServiceOrderStatus($inpFile->status),
            'so_number' => $soNumber,
            'so_short' => $soShort,
            'token' => Str::random(32),
            'created_by' => $this->userCache[$tr->user_id]->id ?? 1,
            'patient_id' => $te->patient_id,
            'service_id' => $te->service_id,
            'doctor_id' => $te->doctor_id,
            'payee_type' => Patient::class,
            'payee_id' => $te->patient_id ?? 0,
            'created_at' => $element->created_on,
            'updated_at' => $element->modified_on ?? now(),
        ]);

        // Link service order to transaction element
        $te->service_order_id = $so->id;
        $te->saveQuietly();
    }

    protected function bindRecesToServiceOrder(object $element, TransactionElement $te): void
    {
        $recesTr = DB::connection('secondary')
            ->table('recestation_transactions')
            ->where('id', $element->department_transaction_id)
            ->first();

        if (! $recesTr || ! $recesTr->treatment_id) {
            MigrationLog::logSkipped('transaction_elements', 'reception_counters_closings_transaction_elements',
                $element->id, 'RECES: No recestation_transaction or missing treatment_id for dept_tr_id: '.$element->department_transaction_id);

            return;
        }

        // treatment_id = inpatient_file.id — find the matching IND service order
        $soShort = 'IND/'.str_pad($recesTr->treatment_id, 8, '0', STR_PAD_LEFT);
        $so = ServiceOrder::where('so_short', $soShort)->first();

        if (! $so) {
            MigrationLog::logSkipped('transaction_elements', 'reception_counters_closings_transaction_elements',
                $element->id, "RECES: No IND service order found for so_short={$soShort} (inpatient_file.id={$recesTr->treatment_id})");

            return;
        }

        $te->service_order_id = $so->id;
        $te->saveQuietly();
    }

    protected function enrichExpenseTransaction(object $tr, array &$data): void
    {
        // Find the old expense record
        $element = DB::connection('secondary')
            ->table('reception_counters_closings_transaction_elements')
            ->where('closing_transaction_id', $tr->id)
            ->first();

        if (! $element || ! $element->department_transaction_id) {
            return;
        }

        $expense = DB::connection('secondary')
            ->table('expenses')
            ->where('id', $element->department_transaction_id)
            ->first();

        if (! $expense) {
            return;
        }

        $data['expense_category_id'] = $this->expenseCategoryCache[$expense->category_id]->id ?? null;
        $data['notes'] = $expense->payment_reference;

        // Handle voucher pay
        if ($expense->voucher_id) {
            $voucher = DB::connection('secondary')
                ->table('expense_vouchers')
                ->where('id', $expense->voucher_id)
                ->first();

            if ($voucher) {
                $existingVoucher = ExpenseVoucher::where('old_id', $voucher->id)->first();

                if (! $existingVoucher) {
                    $vcNumber = $this->generateVcNumber(Carbon::parse($voucher->created_on));

                    $existingVoucher = new ExpenseVoucher;
                    $existingVoucher->vc_number = $vcNumber;
                    $existingVoucher->old_id = $voucher->id;
                    $existingVoucher->exp_category_id = $this->expenseCategoryCache[$voucher->exp_category_id]->id ?? ExpenseCategory::first()->id;
                    $existingVoucher->payed_to = $this->userCache[$voucher->employee_id]->id ?? null;
                    $existingVoucher->payed_to_name = $voucher->payed_to_others;
                    $existingVoucher->amount = abs($voucher->exp_amount_numbers ?? 0);
                    $existingVoucher->notes = $voucher->expense_notes;
                    $existingVoucher->created_at = $voucher->created_on;
                    $existingVoucher->updated_at = $voucher->modified_on ?? now();
                    $existingVoucher->saveQuietly();
                }

                $data['type'] = 'VOUCHER-PAY';
                $data['exp_voucher_id'] = $existingVoucher->id;
            }
        } else {
            $data['type'] = 'EXP';
        }

        // Handle INPT-EXP notes
        if ($element->type === 'INPT-EXP') {
            $inpFileExpense = DB::connection('secondary')
                ->table('inpatient_file_expenses')
                ->where('id', $element->department_transaction_id)
                ->first();

            if ($inpFileExpense) {
                $data['notes'] = $inpFileExpense->payment_reference ?? $data['notes'];
            }
        }
    }

    // =========================================================================
    // 9. Vouchers (standalone — those not linked to a transaction yet)
    // =========================================================================

    protected function syncVouchers(): void
    {
        $cursor = $this->getCursor('vouchers');
        $processed = 0;
        $skipped = 0;
        $total = $this->oldCount('expense_vouchers');

        DB::connection('secondary')
            ->table('expense_vouchers')
            ->where('id', '>', $cursor)
            ->orderBy('id')
            ->chunk($this->batchSize, function ($vouchers) use (&$cursor, &$processed, &$skipped) {
                foreach ($vouchers as $voucher) {
                    if (ExpenseVoucher::where('old_id', $voucher->id)->exists()) {
                        $skipped++;
                        $cursor = $voucher->id;

                        continue;
                    }

                    if (! $this->dryRun) {
                        try {
                            $vcNumber = $this->generateVcNumber(Carbon::parse($voucher->created_on));

                            $newVoucher = new ExpenseVoucher;
                            $newVoucher->vc_number = $vcNumber;
                            $newVoucher->old_id = $voucher->id;
                            $newVoucher->exp_category_id = $this->expenseCategoryCache[$voucher->exp_category_id]->id ?? ExpenseCategory::first()->id;
                            $newVoucher->payed_to = $this->userCache[$voucher->employee_id]->id ?? null;
                            $newVoucher->payed_to_name = $voucher->payed_to_others;
                            $newVoucher->amount = abs($voucher->exp_amount_numbers ?? 0);
                            $newVoucher->notes = $voucher->expense_notes;
                            $newVoucher->created_at = $voucher->created_on;
                            $newVoucher->updated_at = $voucher->modified_on ?? now();
                            $newVoucher->saveQuietly();

                            $processed++;
                        } catch (\Exception $e) {
                            MigrationLog::logError('vouchers', 'expense_vouchers', $voucher->id, $e->getMessage());
                        }
                    }

                    $cursor = $voucher->id;
                }

                $this->setCursor('vouchers', $cursor);
            });

        $this->setCursor('vouchers', $cursor);
        $this->syncDone('vouchers', $processed, $skipped, $total);
    }

    // =========================================================================
    // 10. Treatments — migrate from all department treatment tables
    // =========================================================================

    protected function syncTreatments(): void
    {
        $treatmentTables = [
            ['table' => 'opd_treatments', 'type' => 'OPD', 'patient_col' => 'patient_id'],
            ['table' => 'dental_treatments', 'type' => 'DNT', 'patient_col' => 'patient_id'],
            ['table' => 'emergency_treatments', 'type' => 'EMG', 'patient_col' => 'patient_id'],
            ['table' => 'ultrasound_treatments', 'type' => 'ULT', 'patient_col' => 'patient_id'],
            ['table' => 'xray_treatments', 'type' => 'XRAY', 'patient_col' => 'patient_id'],
            ['table' => 'test_treatments', 'type' => 'PTH', 'patient_col' => 'patient_id'],
            ['table' => 'inpatient_treatments', 'type' => 'IND', 'patient_col' => 'patient_id'],
            ['table' => 'recestation_treatments', 'type' => 'IND', 'patient_col' => 'patient_id'],
        ];

        foreach ($treatmentTables as $tt) {
            $cursorKey = "treatments_{$tt['type']}_{$tt['table']}";
            $cursor = $this->getCursor($cursorKey);
            $processed = 0;
            $skipped = 0;

            // Check if table exists in old DB
            try {
                DB::connection('secondary')->table($tt['table'])->limit(1)->first();
            } catch (\Exception $e) {
                $this->warn("Table {$tt['table']} not found in old DB — skipping.");

                continue;
            }

            $total = $this->oldCount($tt['table']);

            DB::connection('secondary')
                ->table($tt['table'])
                ->where('id', '>', $cursor)
                ->orderBy('id')
                ->chunk($this->batchSize, function ($treatments) use ($tt, &$cursor, &$processed, &$skipped, $cursorKey) {
                    foreach ($treatments as $treatment) {
                        $patientId = $this->resolvePatientId($treatment->{$tt['patient_col']});

                        if (! $patientId) {
                            $skipped++;
                            $cursor = $treatment->id;

                            continue;
                        }

                        // so_short = {DEPT_KEY}/{padded_departmental_sequence}
                        $soShort = strtoupper($tt['type']).'/'.str_pad($treatment->id, 8, '0', STR_PAD_LEFT);
                        if (ServiceOrder::where('so_short', $soShort)->exists()) {
                            $skipped++;
                            $cursor = $treatment->id;

                            continue;
                        }

                        if (! $this->dryRun) {
                            try {
                                $serviceId = $this->resolveServiceId($treatment->service_id ?? null, $tt['type']);
                                $doctorId = isset($treatment->treatment_by) ? ($this->userCache[$treatment->treatment_by]->id ?? null) : null;

                                // Generate SO number
                                $patient = Patient::find($patientId);
                                $soCount = ServiceOrder::where('type', $tt['type'])->count() + 1;
                                $soNumber = ($patient ? $patient->ps_number : 'PS/0000/00/0000').'/'.$tt['type'].'/'.str_pad($soCount, 8, '0', STR_PAD_LEFT);

                                // Build notes JSON from treatment clinical data
                                $notesJson = $this->buildTreatmentNotesJson($treatment, $tt['type']);

                                $so = new ServiceOrder;
                                $so->type = $tt['type'];
                                $so->status = $this->mapServiceOrderStatus($treatment->status ?? 'TREATED');
                                $so->so_number = $soNumber;
                                $so->so_short = $soShort;
                                $so->token = Str::random(32);
                                $so->created_by = $doctorId ?? 1;
                                $so->patient_id = $patientId;
                                $so->service_id = $serviceId;
                                $so->doctor_id = $doctorId;
                                $so->notes = $treatment->description ?? $treatment->name ?? null;
                                $so->notes_json = $notesJson;
                                $so->payee_type = Patient::class;
                                $so->payee_id = $patientId;
                                $so->created_at = $treatment->created_on;
                                $so->updated_at = $treatment->modified_on ?? now();
                                $so->saveQuietly();

                                $processed++;
                            } catch (\Exception $e) {
                                MigrationLog::logError("treatments_{$tt['type']}", $tt['table'], $treatment->id, $e->getMessage());
                            }
                        }

                        $cursor = $treatment->id;
                    }

                    $this->setCursor($cursorKey, $cursor);
                });

            $this->setCursor($cursorKey, $cursor);
            $this->syncDone($tt['table'], $processed, $skipped, $total);
        }
    }

    protected function buildTreatmentNotesJson(object $treatment, string $type): ?array
    {
        $notes = [];

        // Common clinical fields across treatment types
        $clinicalFields = [
            'patient_discomfort', 'patient_bleed_excess', 'already_medication',
            'patient_smoker', 'patient_smoking_frequency', 'is_diabetic',
            'tuberculosis', 'hepatitis', 'epilepsy', 'rheumatic', 'hiv',
            'is_heart_patient', 'is_allergietic', 'prefer_anesthetic',
            'is_pregnant', 'patient_is_first_visit', 'patient_last_visit',
            'patient_last_visit_process', 'patient_physician',
            'patient_last_examination', 'patient_under_medical',
        ];

        foreach ($clinicalFields as $field) {
            if (isset($treatment->{$field}) && $treatment->{$field} !== null && $treatment->{$field} !== '') {
                $notes[$field] = $treatment->{$field};
            }
        }

        // Treatment-specific fields
        if (isset($treatment->name)) {
            $notes['treatment_name'] = $treatment->name;
        }
        if (isset($treatment->description) && $treatment->description) {
            $notes['description'] = $treatment->description;
        }
        if (isset($treatment->treatment_diagnosis_id) && $treatment->treatment_diagnosis_id > 0) {
            // Try to get diagnosis name from old DB
            $diagnosis = DB::connection('secondary')
                ->table('diagnosis')
                ->where('id', $treatment->treatment_diagnosis_id)
                ->value('name');
            if ($diagnosis) {
                $notes['diagnosis'] = $diagnosis;
            }
        }

        // Room info for inpatient
        if (isset($treatment->room_name) && $treatment->room_name) {
            $notes['room'] = $treatment->room_name;
        }

        return empty($notes) ? null : $notes;
    }

    // =========================================================================
    // 11b. Recent Transactions — new transactions added to already-migrated
    //      closings (open counters that received more transactions since the
    //      last full sync). Scoped to closings created within --since window.
    // =========================================================================

    protected function syncRecentTransactions(): void
    {
        $since = $this->option('since')
            ? Carbon::parse($this->option('since'))
            : Carbon::now()->subMonth();

        $this->info("Syncing new transactions for closings since {$since->toDateString()}...");

        $processed = 0;
        $skipped = 0;

        // Fetch only closings within the window from the old DB.
        DB::connection('secondary')
            ->table('reception_counters_closings')
            ->where('created_on', '>=', $since)
            ->orderBy('id')
            ->each(function ($oldClosing) use (&$processed, &$skipped) {
                // Only process closings that are already in the new DB.
                // Brand-new closings (id > by-closings cursor) are handled by
                // syncByClosings(); skip them here to avoid double-migration.
                $newClosing = Closing::where('old_id', $oldClosing->id)->first();

                if (! $newClosing) {
                    return;
                }

                DB::connection('secondary')
                    ->table('reception_counters_closings_transactions')
                    ->where('counter_id', $oldClosing->id)
                    ->orderBy('id')
                    ->each(function ($tr) use ($newClosing, &$processed, &$skipped) {
                        if (Transaction::where('old_id', $tr->id)->exists()) {
                            $skipped++;

                            return;
                        }

                        if (! $this->dryRun) {
                            try {
                                $this->migrateTransaction($tr, $newClosing->id);
                                $processed++;
                            } catch (\Exception $e) {
                                MigrationLog::logError(
                                    'recent-transactions',
                                    'reception_counters_closings_transactions',
                                    $tr->id,
                                    $e->getMessage()
                                );
                                $this->warn("Transaction {$tr->id} error: ".$e->getMessage());
                            }
                        }
                    });
            });

        $this->syncDone('recent-transactions', $processed, $skipped, 0);
    }

    // =========================================================================
    // Number generation helpers — counters initialized once per key from DB,
    // then incremented in-memory to avoid a COUNT query per generated number.
    // =========================================================================

    protected function generateCtNumber(Carbon $date): string
    {
        $key = $date->format('Y/m');
        if (! isset($this->ctCounters[$key])) {
            $this->ctCounters[$key] = Closing::where('ct_number', 'like', "CT/{$key}/%")->count();
        }

        return 'CT/'.$key.'/'.str_pad(++$this->ctCounters[$key], 4, '0', STR_PAD_LEFT);
    }

    protected function generateTrNumber(Carbon $date): string
    {
        $key = $date->format('Y/m/d');
        if (! isset($this->trCounters[$key])) {
            $this->trCounters[$key] = Transaction::where('tr_number', 'like', "TR/{$key}/%")->count();
        }

        return 'TR/'.$key.'/'.str_pad(++$this->trCounters[$key], 4, '0', STR_PAD_LEFT);
    }

    protected function generateVcNumber(Carbon $date): string
    {
        $key = $date->format('Y/m');
        if (! isset($this->vcCounters[$key])) {
            $this->vcCounters[$key] = ExpenseVoucher::where('vc_number', 'like', "VC/{$key}/%")->count();
        }

        return 'VC/'.$key.'/'.str_pad(++$this->vcCounters[$key], 4, '0', STR_PAD_LEFT);
    }

    protected function generatePsNumber(Carbon $date): string
    {
        $key = $date->format('Y/m');
        if (! isset($this->psCounters[$key])) {
            $this->psCounters[$key] = Patient::where('ps_number', 'like', "PS/{$key}/%")->count();
        }

        return 'PS/'.$key.'/'.str_pad(++$this->psCounters[$key], 4, '0', STR_PAD_LEFT);
    }

    // =========================================================================
    // Mapping helpers
    // =========================================================================

    protected function mapPaymentType(string $type): string
    {
        return match ($type) {
            'CARD', 'CREDITCARD' => 'CARD',
            'CHEQUE' => 'CHEQUE',
            default => 'CASH',
        };
    }

    protected function mapElementType(string $type): string
    {
        return match ($type) {
            'OPD' => 'OPD',
            'INPT' => 'IND',
            'EMER' => 'EMG',
            'DENTAL' => 'DNT',
            'ULTRA' => 'ULT',
            'XRAY' => 'RAD',
            'LAB' => 'LAB',
            default => $type,
        };
    }

    protected function mapServiceOrderStatus(string $status): string
    {
        return match (strtoupper($status)) {
            'OPEN', 'ACTIVE' => ServiceOrderStatus::OPEN->name,
            // DISCHARGED is the inpatient-specific terminal state — maps to CLOSED
            'CLOSED', 'DISCHARGED', 'DISCHARGE' => ServiceOrderStatus::CLOSED->name,
            'IN_PROGRESS', 'IN-PROGRESS' => ServiceOrderStatus::IN_PROGRESS->name,
            'TREATED' => ServiceOrderStatus::TREATED->name,
            'CANCELLED' => ServiceOrderStatus::CANCELLED->name,
            'REFUNDED' => ServiceOrderStatus::REFUNDED->name,
            default => ServiceOrderStatus::CLOSED->name,
        };
    }

    // =========================================================================
    // Resolution helpers — resolve foreign keys with cache + on-demand creation
    // =========================================================================

    protected function resolvePatientId(?int $id): ?int
    {
        if (! $id) {
            return null;
        }

        if (isset($this->patientCache[$id])) {
            return $this->patientCache[$id]->id;
        }

        $patient = Patient::find($id);
        if ($patient) {
            $this->patientCache[$id] = $patient;

            return $patient->id;
        }

        // On-demand migration from old DB (used in by-closings flow)
        $oldPatient = DB::connection('secondary')
            ->table('patients')
            ->where('id', $id)
            ->first();

        if (! $oldPatient) {
            return null;
        }

        try {
            $createdAt = Carbon::parse($oldPatient->created_on);
            $psNumber = $this->generatePsNumber($createdAt);

            $contact = $this->validatePhoneNumber($oldPatient->patient_contact_mobile)
                ? $this->formatPhoneNumber($oldPatient->patient_contact_mobile)
                : null;

            $cnic = $this->validateCnic($oldPatient->patient_cnic)
                ? $this->formatCnic($oldPatient->patient_cnic)
                : null;

            $address = is_string($oldPatient->patient_address) ? $oldPatient->patient_address : null;

            $newPatient = new Patient;
            $newPatient->id = $oldPatient->id;
            $newPatient->ps_number = $psNumber;
            $newPatient->name = $oldPatient->pateint_name;
            $newPatient->gender = $oldPatient->gender;
            $newPatient->age_group = $oldPatient->age_group ?: null;
            $newPatient->age_days = $oldPatient->age_days ?: null;
            $newPatient->age_dob = $this->sanitizeDate($oldPatient->age_dob);
            $newPatient->address = $address;
            $newPatient->guardian = $oldPatient->guardian;
            $newPatient->relation = $oldPatient->relation;
            $newPatient->contact = $contact;
            $newPatient->cnic = $cnic;
            $newPatient->created_at = $oldPatient->created_on;
            $newPatient->updated_at = $oldPatient->modified_on ?? now();
            $newPatient->saveQuietly();

            $this->patientCache[$id] = $newPatient;

            return $newPatient->id;
        } catch (\Exception $e) {
            MigrationLog::logError('patients', 'patients', $id, $e->getMessage());

            return null;
        }
    }

    protected function resolveClosingId(?int $id): ?int
    {
        if (! $id || $id == 0) {
            return null;
        }

        if (isset($this->closingCache[$id])) {
            return $this->closingCache[$id]->id;
        }

        // Try finding in new DB by old_id or id
        $closing = Closing::where('old_id', $id)->orWhere('id', $id)->first();
        if ($closing) {
            $this->closingCache[$id] = $closing;

            return $closing->id;
        }

        // On-demand migration from old DB
        $oldClosing = DB::connection('secondary')
            ->table('reception_counters_closings')
            ->where('id', $id)
            ->first();

        if (! $oldClosing) {
            return null;
        }

        $receptionId = $this->oldReceptionIdMap[$oldClosing->reception_id] ?? ($this->receptionCache[$oldClosing->reception_id]->id ?? null);
        $userId = $this->userCache[$oldClosing->user_id]->id ?? null;

        if (! $receptionId || ! $userId) {
            return null;
        }

        $ctNumber = $this->generateCtNumber(Carbon::parse($oldClosing->cash_recieving_time ?? $oldClosing->created_on));

        $newClosing = new Closing;
        $newClosing->id = $oldClosing->id;
        $newClosing->old_id = $oldClosing->id;
        $newClosing->reception_id = $receptionId;
        $newClosing->receptionist_id = $userId;
        $newClosing->ct_number = $ctNumber;
        $newClosing->status = $oldClosing->status === 'CLOSED' ? CounterStatus::REPORTED : CounterStatus::OPEN;
        $newClosing->opening_amount = $oldClosing->opening_amount ?? 0;
        $newClosing->closing_amount = $oldClosing->closing_amount ?? 0;
        $newClosing->closing_amount_cash = $oldClosing->closing_amount_cash ?? 0;
        $newClosing->closing_amount_cheque = 0;
        $newClosing->closing_amount_card = ($oldClosing->closing_amount_card ?? 0) + ($oldClosing->closing_amount_creditcard ?? 0);
        $newClosing->expense_payed = $oldClosing->expense_payed ?? 0;
        $newClosing->amount_received = $oldClosing->closing_amount ?? 0;
        $newClosing->closed_at = $oldClosing->cash_recieving_time;
        $newClosing->cash_recieving_time = $oldClosing->cash_recieving_time;
        $newClosing->reported_by = $userId;
        $newClosing->created_at = $oldClosing->created_on;
        $newClosing->updated_at = $oldClosing->modified_on ?? now();
        $newClosing->saveQuietly();

        $this->closingCache[$id] = $newClosing;

        return $newClosing->id;
    }

    protected function resolveServiceId(?int $oldId, string $deptSlug): ?int
    {
        if (! $oldId) {
            return null;
        }

        $key = "{$deptSlug}_{$oldId}";
        if (isset($this->serviceCache[$key])) {
            return $this->serviceCache[$key]->id;
        }

        $dept = $this->serviceDeptCache[$deptSlug] ?? null;
        if (! $dept) {
            return null;
        }

        $service = Service::where('old_id', $oldId)
            ->where('service_department_id', $dept->id)
            ->first();

        if ($service) {
            $this->serviceCache[$key] = $service;

            return $service->id;
        }

        return null;
    }

    // =========================================================================
    // Validation / sanitization helpers
    // =========================================================================

    protected function sanitizeAmount($value, bool $isExpense = false): float
    {
        if ($value === null) {
            return 0;
        }

        $numericValue = is_numeric($value) ? (float) $value : 0;

        // Detect corrupt max-int values
        if (abs($numericValue) >= 2147483647) {
            $this->warn("Corrupt value detected: {$numericValue}, setting to 0");

            return 0;
        }

        // Clamp to reasonable hospital transaction range
        $max = 10000000; // 10 million
        if (abs($numericValue) > $max) {
            $this->warn("Value {$numericValue} exceeds reasonable limit, clamping to {$max}");

            return $numericValue > 0 ? $max : -$max;
        }

        // For expenses, ensure positive storage
        if ($isExpense && $numericValue < 0) {
            return abs($numericValue);
        }

        return $numericValue;
    }

    protected function sanitizeDate(mixed $value): ?string
    {
        if ($value === null || $value === '' || $value === false) {
            return null;
        }

        $stringVal = (string) $value;

        // Reject MySQL zero dates in any format
        if (str_contains($stringVal, '0000-00-00')) {
            return null;
        }

        // Validate it's actually parseable
        try {
            Carbon::parse($stringVal);

            return $stringVal;
        } catch (\Exception) {
            return null;
        }
    }

    protected function validatePhoneNumber(?string $number): bool
    {
        if (! $number) {
            return false;
        }
        $number = preg_replace('/\D/', '', $number);

        return (Str::startsWith($number, '92') && strlen($number) == 12)
            || (Str::startsWith($number, '0') && strlen($number) == 11)
            || (Str::startsWith($number, '3') && strlen($number) == 10);
    }

    protected function formatPhoneNumber(string $number): string
    {
        $number = preg_replace('/\D/', '', $number);

        if (Str::startsWith($number, '92')) {
            return '+92-'.substr($number, 2, 3).'-'.substr($number, 5);
        }
        if (Str::startsWith($number, '0')) {
            return '+92-'.substr($number, 1, 3).'-'.substr($number, 4);
        }
        if (Str::startsWith($number, '3') && strlen($number) == 10) {
            return '+92-'.substr($number, 0, 3).'-'.substr($number, 3);
        }

        return $number;
    }

    protected function validateCnic(?string $cnic): bool
    {
        if (! $cnic) {
            return false;
        }

        return strlen(preg_replace('/\D/', '', $cnic)) == 13;
    }

    protected function formatCnic(string $cnic): string
    {
        $cnic = preg_replace('/\D/', '', $cnic);
        if (strlen($cnic) == 13) {
            return substr($cnic, 0, 5).'-'.substr($cnic, 5, 7).'-'.substr($cnic, 12);
        }

        return $cnic;
    }

    // =========================================================================
    // 11. Abacus Closings — create Incoming + double-entry transactions per closing
    // =========================================================================

    protected function syncAbacusClosings(): void
    {
        $service = new AbacusClosingService;

        $processed = 0;
        $skipped = 0;

        Closing::query()
            ->whereDoesntHave('abacusIncoming')
            ->orderBy('id')
            ->chunk($this->batchSize, function ($closings) use (&$processed, &$skipped, $service) {
                foreach ($closings as $closing) {
                    if ($this->dryRun) {
                        $processed++;

                        continue;
                    }

                    try {
                        $service->createEntriesForClosing($closing);
                        $processed++;
                    } catch (\Exception $e) {
                        MigrationLog::logError('abacus-closings', 'closings', $closing->id, $e->getMessage());
                        $this->warn("Abacus closing {$closing->id} ({$closing->ct_number}) error: ".$e->getMessage());
                        $skipped++;
                    }
                }

                $this->info("Abacus closings batch: +{$processed} processed, {$skipped} skipped");
            });

        $this->syncDone('abacus-closings', $processed, $skipped, Closing::count());
    }

    // =========================================================================
    // Summary helpers
    // =========================================================================

    protected function syncDone(string $entity, int $migrated, int $skipped, int $total): void
    {
        $left = max(0, $total - $migrated - $skipped);
        $this->info("{$entity}: {$migrated} migrated · {$skipped} skipped · {$left} left / {$total} total");
    }

    protected function oldCount(string $table, array $where = []): int
    {
        $q = DB::connection('secondary')->table($table);
        foreach ($where as $col => $val) {
            $q->where($col, $val);
        }

        return (int) $q->count();
    }

    protected function printSyncSummary(): void
    {
        $this->newLine(2);
        $this->info('═══════════════════════════════════════');
        $this->info('           SYNC SUMMARY');
        $this->info('═══════════════════════════════════════');

        $cursors = UpgradeProcess::where('name', 'like', 'sync_cursor_%')->get();
        foreach ($cursors as $c) {
            $entity = str_replace('sync_cursor_', '', $c->name);
            $this->info(str_pad($entity, 30)."cursor: {$c->value}");
        }

        $this->newLine();
        $this->info('New DB record counts:');

        $counts = [
            'Users' => User::count(),
            'Patients' => Patient::count(),
            'Services' => Service::count(),
            'Receptions' => Reception::count(),
            'Panels' => Panel::count(),
            'Closings' => Closing::count(),
            'Transactions' => Transaction::count(),
            'Trans. Elements' => TransactionElement::count(),
            'Expense Vouchers' => ExpenseVoucher::count(),
            'Service Orders' => ServiceOrder::count(),
            'Abacus Incomings' => AbacusIncoming::count(),
            'Abacus Entries' => AbacusTransaction::count(),
        ];

        foreach ($counts as $label => $count) {
            $this->info(str_pad($label, 20).$count);
        }

        $this->info('═══════════════════════════════════════');
    }
}
