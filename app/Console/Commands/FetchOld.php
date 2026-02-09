<?php

namespace App\Console\Commands;

use App\Enum\CounterStatus;
use App\Models\Closing;
use App\Models\UpgradeProcess;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Dentist;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\Image;
use App\Models\IndDoctor;
use App\Models\OpdDoctor;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\ServiceRecestation;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use App\Models\Administrator;
use App\Models\Receptionist;
use App\Models\EmergencyDoctor;
use App\Models\UltrasoundDoctor;
use App\Models\XrayTechnician;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\MigrationLog;

class FetchOld extends Command
{
    public static $TOTAL_STEPS = 82;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:fetch-old {--step=} {--reset} {--batch-size=2000}';

    /**
     * The console command description.
     */
    protected $description = 'Optimized data migration from old schema database to new schema';

    // Cache collections for frequently accessed data
    protected $userCache = [];
    protected $patientCache = [];
    protected $serviceCache = [];
    protected $serviceRecesitationCache = [];
    protected $serviceDeptCache = [];
    protected $receptionCache = [];
    protected $closingCache = [];
    protected $expenseCache = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        Log::info('Starting optimized fetch-old command execution.');

        // Set memory and time limits
        ini_set('max_execution_time', 7200); // 2 hours
        set_time_limit(0);
        ini_set('memory_limit', '2G');

        // Optimize database connections
        DB::connection('secondary')->getPdo()->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        DB::getPdo()->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);

        $batchSize = $this->option('batch-size');

        // Initialize migration batch for logging
        $batchId = 'batch_' . now()->format('Y_m_d_H_i_s') . '_' . uniqid();
        Cache::put('migration_batch_id', $batchId, 3600); // Store for 1 hour
        
        $this->info("🔄 Starting migration batch: {$batchId}");

        try {
            DB::connection('secondary')->getPdo();
            MigrationLog::logAction('system', MigrationLog::ACTION_SUCCESS, [
                'reason' => 'Secondary database connection established',
                'batch_id' => $batchId
            ]);
        } catch (\Exception $e) {
            Log::error('Secondary database connection failed: ' . $e->getMessage());
            MigrationLog::logError('system', null, null, $e->getMessage());
            $this->error('Secondary database connection failed: ' . $e->getMessage());
            return 1;
        }

        $statusObj = UpgradeProcess::firstOrCreate([
            'name' => 'currentStep'
        ], [
            'value' => 0
        ]);

        if ($this->option('reset')) {
            $statusObj->value = 0;
            $statusObj->save();
            MigrationLog::logAction('system', MigrationLog::ACTION_SUCCESS, [
                'reason' => 'Migration step reset to 0',
                'batch_id' => $batchId
            ]);
            $this->info('Migration step reset to 0');
        }

        if ($this->option('step')) {
            $statusObj->value = $this->option('step');
            $statusObj->save();
            MigrationLog::logAction('system', MigrationLog::ACTION_SUCCESS, [
                'reason' => "Migration step manually set to {$this->option('step')}",
                'batch_id' => $batchId
            ]);
            $this->info('Migration step set to ' . $this->option('step'));
        }

        $currentStep = $statusObj->value;

        // Preload frequently accessed data
        $this->preloadCacheData();

        // Execute migration steps
        $this->executeStep($currentStep, $batchSize);

        if ($currentStep >= self::$TOTAL_STEPS) {
            $this->info('Migration completed successfully!');
            return 0;
        }

        $statusObj->value = $currentStep + 1;
        $statusObj->save();

        $this->info("Completed step {$currentStep}, next step: " . ($currentStep + 1));
        return 0;
    }

    /**
     * Preload frequently accessed data to reduce database queries
     */
    protected function preloadCacheData()
    {
        Log::info('Stage: Preloading cache data.');
        $this->info('Preloading cache data...');

        // Cache all users
        $users = User::all(['id', 'email', 'name']);
        foreach ($users as $user) {
            $this->userCache[$user->id] = $user;
        }

        // Cache all service departments
        ServiceDepartment::all()->each(function ($dept) {
            Cache::put("service_dept_{$dept->slug}", $dept, 3600);
        });

        // Cache all receptions
        $receptions = Reception::all(['id', 'name']);
        foreach ($receptions as $reception) {
            $this->receptionCache[$reception->id] = $reception;
        }

        $this->info('Cache preloading completed.');
    }

    /**
     * Execute migration step with optimizations
     */
    protected function executeStep($currentStep, $batchSize)
    {
        Log::info("Stage: Executing migration step {$currentStep}.");
        
        switch ($currentStep) {
            case 1:
                $this->imagesOptimized($batchSize);
                break;
            case 2:
                $this->usersOptimized($batchSize);
                break;
            case 3:
                $this->servicesOptimized($batchSize);
                break;
            case 4:
                $this->receptionsOptimized($batchSize);
                break;
            case 5:
                $this->patientsOptimized(2020, null, $batchSize);
                break;
            case 6:
            case 7:
            case 8:
            case 9:
            case 10:
            case 11:
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
                $month = $currentStep - 5;
                $this->patientsOptimized(2021, $month, $batchSize);
                break;
            case 18:
            case 19:
            case 20:
            case 21:
            case 22:
            case 23:
            case 24:
            case 25:
            case 26:
            case 27:
            case 28:
            case 29:
                $month = $currentStep - 17;
                $this->patientsOptimized(2022, $month, $batchSize);
                break;
            case 30:
            case 31:
            case 32:
            case 33:
            case 34:
            case 35:
            case 36:
            case 37:
            case 38:
            case 39:
            case 40:
            case 41:
                $month = $currentStep - 29;
                $this->patientsOptimized(2023, $month, $batchSize);
                break;
            case 42:
            case 43:
            case 44:
            case 45:
            case 46:
            case 47:
            case 48:
            case 49:
            case 50:
            case 51:
            case 52:
            case 53:
                $month = $currentStep - 41;
                $this->patientsOptimized(2024, $month, $batchSize);
                break;
            case 54:
            case 55:
            case 56:
            case 57:
            case 58:
            case 59:
            case 60:
            case 61:
            case 62:
            case 63:
            case 64:
            case 65:
                $month = $currentStep - 53;
                $this->patientsOptimized(2025, $month, $batchSize);
                break;
            case 66:
            case 67:
            case 68:
            case 69:
            case 70:
            case 71:
            case 72:
            case 73:
            case 74:
            case 75:
            case 76:
            case 77:
                $month = $currentStep - 65;
                $this->patientsOptimized(2026, $month, $batchSize);
                break;
            case 78:
                $this->counterClosingsOptimized($batchSize);
                break;
            case 79:
                $this->expenseCategoriesOptimized($batchSize);
                break;
            case 80:
                $this->vouchersOptimized($batchSize);
                break;
            case 81:
                $this->expensesOptimized($batchSize);
                break;
            case 82:
                $this->counterClosingTransactionsOptimized($batchSize);
                break;
            default:
                break;
        }
    }

    /**
     * Optimized images migration
     */
    protected function imagesOptimized($batchSize)
    {
        $this->info('Migrating images...');
        
        DB::connection('secondary')
            ->table('images')
            ->orderBy('id')
            ->chunk($batchSize, function ($images) {
                $insertData = [];
                
                foreach ($images as $image) {
                    $insertData[] = [
                        'path' => $image->path,
                        'owner_id' => $image->owner_id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                // Bulk insert with ignore to handle duplicates
                if (!empty($insertData)) {
                    Image::insertOrIgnore($insertData);
                    $this->info('Processed ' . count($insertData) . ' images');
                }
            });
    }

    /**
     * Optimized users migration with bulk operations
     */
    protected function usersOptimized($batchSize)
    {
        $this->info('Migrating users...');
        $batchId = Cache::get('migration_batch_id');
        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalErrors = 0;
        
        DB::connection('secondary')
            ->table('aauth_users')
            ->orderBy('id')
            ->chunk($batchSize, function ($users) use (&$totalProcessed, &$totalSkipped, &$totalErrors, $batchId) {
                $insertData = [];
                $profileData = [];
                
                foreach ($users as $user) {
                    try {
                        // Validate user data
                        if (empty($user->email) || !filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
                            MigrationLog::logSkipped('users', 'aauth_users', $user->id, 'Invalid email address', (array)$user);
                            $totalSkipped++;
                            continue;
                        }

                        if (empty($user->name)) {
                            MigrationLog::logSkipped('users', 'aauth_users', $user->id, 'Empty name field', (array)$user);
                            $totalSkipped++;
                            continue;
                        }

                        $userData = [
                            'id' => $user->id,
                            'name' => $user->name,
                            'email' => $user->email,
                            'password' => Hash::make('password'),
                            'password_expired_at' => Carbon::now(),
                            'is_active' => $user->banned == 0 ? 1 : 0,
                            'banned_message' => $user->banned_message,
                            'last_login' => $user->last_login,
                            'last_activity' => $user->last_activity,
                            'last_login_attempt' => $user->last_login_attempt,
                            'ip_address' => $user->ip_address,
                            'login_attempts' => $user->login_attempts ?? 0,
                            'profile_img_path' => ltrim($user->profile_img_path, 'public/'),
                            'profile_img_id' => $user->profile_img_id,
                            'created_at' => $user->created_on,
                            'updated_at' => $user->modified_on,
                        ];

                        $insertData[] = $userData;

                        // Cache user for later use
                        $userObj = (object)[
                            'id' => $user->id,
                            'email' => $user->email,
                            'name' => $user->name
                        ];
                        $this->userCache[$user->id] = $userObj;

                        // Collect profile data for bulk insert later
                        if ($user->is_super_admin) $profileData['admin'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_receptionist) $profileData['receptionist'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_opd_doctor) $profileData['opd_doctor'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_inpatient_doctor) $profileData['ind_doctor'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_emergency_doctor) $profileData['emergency_doctor'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_dentist) $profileData['dentist'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_ultrasound_doc) $profileData['ultrasound_doctor'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];
                        if ($user->is_xray_tech) $profileData['xray_technician'][] = ['user_id' => $user->id, 'created_at' => $user->created_on, 'updated_at' => $user->modified_on];

                        $totalProcessed++;

                    } catch (\Exception $e) {
                        MigrationLog::logError('users', 'aauth_users', $user->id, $e->getMessage(), (array)$user);
                        $totalErrors++;
                        continue;
                    }
                }
                
                // Bulk insert users
                if (!empty($insertData)) {
                    try {
                        $inserted = User::insertOrIgnore($insertData);
                        $this->info('Processed ' . count($insertData) . ' users in this batch');
                        
                        // Log successful batch
                        MigrationLog::logAction('users', MigrationLog::ACTION_SUCCESS, [
                            'reason' => 'Bulk insert completed',
                            'old_table' => 'aauth_users',
                            'new_table' => 'users',
                            'batch_id' => $batchId,
                            'old_data' => ['batch_count' => count($insertData)]
                        ]);

                    } catch (\Exception $e) {
                        MigrationLog::logError('users', 'aauth_users', null, $e->getMessage(), [
                            'batch_size' => count($insertData),
                            'batch_id' => $batchId
                        ]);
                        $this->error('Failed to insert users batch: ' . $e->getMessage());
                    }
                }

                // Bulk insert profiles
                $this->bulkInsertProfiles($profileData, $batchId);
            });

        // Log final summary
        MigrationLog::logAction('users', MigrationLog::ACTION_SUCCESS, [
            'reason' => 'Users migration completed',
            'old_table' => 'aauth_users', 
            'new_table' => 'users',
            'batch_id' => $batchId,
            'old_data' => [
                'total_processed' => $totalProcessed,
                'total_skipped' => $totalSkipped,
                'total_errors' => $totalErrors
            ]
        ]);

        $this->info("✅ Users migration completed: {$totalProcessed} processed, {$totalSkipped} skipped, {$totalErrors} errors");
    }

    /**
     * Bulk insert user profiles
     */
    protected function bulkInsertProfiles($profileData, $batchId)
    {
        $profileModels = [
            'admin' => Administrator::class,
            'receptionist' => Receptionist::class,
            'opd_doctor' => OpdDoctor::class,
            'ind_doctor' => IndDoctor::class,
            'emergency_doctor' => EmergencyDoctor::class,
            'dentist' => Dentist::class,
            'ultrasound_doctor' => UltrasoundDoctor::class,
            'xray_technician' => XrayTechnician::class,
        ];

        foreach ($profileData as $type => $profiles) {
            if (!empty($profiles) && isset($profileModels[$type])) {
                try {
                    $profileModels[$type]::insertOrIgnore($profiles);
                    MigrationLog::logAction("user_profiles_{$type}", MigrationLog::ACTION_SUCCESS, [
                        'reason' => "Successfully inserted {$type} profiles",
                        'old_table' => 'aauth_users',
                        'new_table' => $type,
                        'batch_id' => $batchId,
                        'old_data' => ['profile_count' => count($profiles)]
                    ]);
                } catch (\Exception $e) {
                    MigrationLog::logError("user_profiles_{$type}", 'aauth_users', null, $e->getMessage(), [
                        'profile_type' => $type,
                        'profile_count' => count($profiles),
                        'batch_id' => $batchId
                    ]);
                }
            }
        }
    }

    /**
     * Optimized services migration
     */
    protected function servicesOptimized($batchSize)
    {
        $this->info('Migrating services...');
        
        $serviceTypes = [
            ['key' => 'OPD', 'name' => "Outdoor", 'image' => "/img/opd.png", 'table' => 'opd_services'],
            ['key' => 'IND', 'name' => "Indoor", 'image' => "/img/ind.png", 'table' => 'inpd_services', 'recesitation_table' => 'recestation_services'],
            ['key' => 'EMG', 'name' => "Emergency", 'image' => "/img/emergency.png", 'table' => 'emergency_services'],
            ['key' => 'DNT', 'name' => "Dental Department", 'image' => "/img/dental.png", 'table' => 'dental_services'],
            ['key' => 'PTH', 'name' => "Laboratory", 'image' => "/img/laboratory.png", 'table' => 'test_services'],
            ['key' => 'ULT', 'name' => "Ultrasound", 'image' => "/img/ultrasound.png", 'table' => 'ultrasound_services'],
            ['key' => 'XRY', 'name' => "Radiology", 'image' => "/img/xray.png", 'table' => 'xray_services']
        ];

        foreach ($serviceTypes as $serviceType) {
            $department = ServiceDepartment::updateOrCreate(
                ['slug' => $serviceType['key']],
                [
                    'name' => $serviceType['name'],
                    'image' => $serviceType['image'],
                    'have_composit_services' => $serviceType['key'] === 'IND'
                ]
            );

            // Migrate main services
            DB::connection('secondary')
                ->table($serviceType['table'])
                ->orderBy('id')
                ->chunk($batchSize, function ($services) use ($department, $serviceType) {
                    $insertData = [];
                    
                    foreach ($services as $service) {
                        $serviceProviderTypes = [];
                        
                        if ($service->is_doctor_selectable) {
                            switch ($serviceType['key']) {
                                case 'OPD':
                                    $serviceProviderTypes = [OpdDoctor::class];
                                    break;
                                case 'IND':
                                    $serviceProviderTypes = [IndDoctor::class];
                                    break;
                                case 'DNT':
                                    $serviceProviderTypes = [Dentist::class];
                                    break;
                            }
                        }

                        $insertData[] = [
                            'name' => $service->name,
                            'slug' => $service->post_key,
                            'service_department_id' => $department->id,
                            'charges' => $service->charges,
                            'charges_include_tax' => $service->charges_including_tax,
                            'tax_rate' => $service->tax_rate,
                            'have_service_provider' => in_array($department->slug, ['OPD', 'IND']) && $service->is_doctor_selectable,
                            'is_composit_service' => $department->have_composit_services,
                            'service_provider_types' => json_encode($serviceProviderTypes),
                            'created_by' => $service->entered_by,
                            'created_at' => now(),
                            'updated_at' => now(),
                            'old_id' => $service->id
                        ];
                    }
                    
                    if (!empty($insertData)) {
                        Service::insert($insertData);
                        $this->info("Processed " . count($insertData) . " {$serviceType['key']} services");
                    }
                });

            // Migrate recestation services if applicable
            if (isset($serviceType['recesitation_table'])) {
                DB::connection('secondary')
                    ->table($serviceType['recesitation_table'])
                    ->orderBy('id')
                    ->chunk($batchSize, function ($services) use ($department) {
                        $insertData = [];
                        
                        foreach ($services as $service) {
                            $insertData[] = [
                                'name' => $service->name,
                                'slug' => $service->post_key == 0 ? null : $service->post_key,
                                'service_department_id' => $department->id,
                                'charges' => $service->charges,
                                'charges_include_tax' => $service->charges_including_tax,
                                'tax_rate' => $service->tax_rate,
                                'created_by' => $service->entered_by,
                                'created_at' => now(),
                                'updated_at' => now(),
                                'old_id' => $service->id
                            ];
                        }
                        
                        if (!empty($insertData)) {
                            ServiceRecestation::insert($insertData);
                            $this->info("Processed " . count($insertData) . " recestation services");
                        }
                    });
            }
        }
    }

    /**
     * Optimized receptions migration
     */
    protected function receptionsOptimized($batchSize)
    {
        $this->info('Migrating receptions...');
        
        DB::connection('secondary')
            ->table('reception_counters')
            ->orderBy('id')
            ->chunk($batchSize, function ($receptions) {
                $insertData = [];
                
                foreach ($receptions as $reception) {
                    $allowedDepartments = [];
                    if ($reception->is_opd_allowed) $allowedDepartments[] = 'OPD';
                    if ($reception->is_inpatient_allowed) $allowedDepartments[] = 'IND';
                    if ($reception->is_emergency_allowed) $allowedDepartments[] = 'EMG';
                    
                    // Always add these departments
                    $allowedDepartments = array_merge($allowedDepartments, ['DNT', 'PTH', 'ULT', 'XRY']);

                    $insertData[] = [
                        'id' => $reception->id,
                        'name' => $reception->counter_name,
                        'allowed_departments' => json_encode($allowedDepartments),
                        'is_allowed_to_pay_voucher' => $reception->is_allowed_to_pay_voucher,
                        'is_allowed_to_pay_from_petty_cash' => $reception->is_allowed_to_pay_from_petty_cash,
                        'is_cash_allowed' => $reception->cash_on_counter,
                        'is_cheques_allowed' => $reception->cheques_on_counter,
                        'is_card_allowed' => $reception->card_slips_on_counter,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];

                    // Cache reception
                    $this->receptionCache[$reception->id] = (object)[
                        'id' => $reception->id,
                        'name' => $reception->counter_name
                    ];
                }
                
                if (!empty($insertData)) {
                    Reception::insertOrIgnore($insertData);
                    $this->info('Processed ' . count($insertData) . ' receptions');
                }
            });
    }

    /**
     * Optimized patients migration with better chunking
     */
    protected function patientsOptimized($year, $month = null, $batchSize)
    {
        $this->info("Migrating patients for year {$year}" . ($month ? " month {$month}" : ""));
        
        $query = DB::connection('secondary')->table('patients');
        
        if ($month) {
            $query->whereMonth('created_on', $month);
        }
        
        $query->whereYear('created_on', $year);

        // Get existing patient count for PS number generation
        $existingCount = Patient::whereYear('created_at', $year)
            ->when($month, fn($q) => $q->whereMonth('created_at', $month))
            ->count();

        $counter = $existingCount;

        $query->orderBy('id')->chunk($batchSize, function ($patients) use ($year, $month, &$counter) {
            $insertData = [];
            
            foreach ($patients as $patient) {
                $counter++;
                $createdInTheMonth = Carbon::parse($patient->created_on);
                $psNumber = 'PS/' . $createdInTheMonth->format('Y/m') . '/' . str_pad($counter, 6, '0', STR_PAD_LEFT);

                $insertData[] = [
                    'id' => $patient->id,
                    'name' => $patient->pateint_name,
                    'ps_number' => $psNumber,
                    'gender' => $patient->gender,
                    'age_group' => null,
                    'age_days' => null,
                    'age_dob' => null,
                    'address' => $patient->patient_address,
                    'guardian' => $patient->guardian,
                    'relation' => $patient->relation,
                    'contact' => $patient->patient_contact_mobile,
                    'cnic' => $patient->patient_cnic,
                    'created_at' => $patient->created_on,
                    'updated_at' => $patient->modified_on,
                ];

                // Cache patient
                $this->patientCache[$patient->id] = (object)[
                    'id' => $patient->id,
                    'name' => $patient->pateint_name
                ];
            }
            
            if (!empty($insertData)) {
                Patient::insertOrIgnore($insertData);
                $this->info('Processed ' . count($insertData) . ' patients');
            }
        });
    }

    /**
     * Optimized counter closings migration
     */
    protected function counterClosingsOptimized($batchSize)
    {
        $this->info('Migrating counter closings...');
        
        DB::connection('secondary')
            ->table('reception_counters_closings')
            ->orderBy('id')
            ->chunk($batchSize, function ($closings) {
                $insertData = [];
                
                foreach ($closings as $closing) {
                    $createdDate = Carbon::parse($closing->created_on);
                    
                    // Get existing count for CT number generation
                    $countInMonth = Closing::whereYear('created_at', $createdDate->year)
                        ->whereMonth('created_at', $createdDate->month)
                        ->count();

                    $ctNumber = 'CT/' . $createdDate->format('Y/m/') . str_pad($countInMonth + 1, 4, '0', STR_PAD_LEFT);

                    $insertData[] = [
                        'id' => $closing->id,
                        'old_id' => $closing->id,
                        'reception_id' => $this->getCachedReception($closing->reception_id)?->id,
                        'receptionist_id' => $this->getCachedUser($closing->user_id)?->id,
                        'ct_number' => $ctNumber,
                        'status' => $closing->status == 'CLOSED' ? CounterStatus::REPORTED : CounterStatus::OPEN,
                        'opening_amount' => $closing->opening_amount,
                        'closing_amount' => $closing->closing_amount,
                        'closing_amount_cash' => $closing->closing_amount_cash,
                        'closing_amount_cheque' => 0,
                        'closing_amount_card' => ($closing->closing_amount_card ?? 0) + ($closing->closing_amount_creditcard ?? 0),
                        'expense_payed' => $closing->expense_payed,
                        'closed_at' => $closing->cash_recieving_time,
                        'reported_by' => 31,
                        'amount_received' => $closing->closing_amount,
                        'cash_recieving_time' => $closing->cash_recieving_time,
                        'created_at' => $closing->created_on,
                        'updated_at' => $closing->modified_on,
                    ];

                    // Cache closing
                    $this->closingCache[$closing->id] = (object)[
                        'id' => $closing->id
                    ];
                }
                
                if (!empty($insertData)) {
                    Closing::insertOrIgnore($insertData);
                    $this->info('Processed ' . count($insertData) . ' closings');
                }
            });
    }

    /**
     * Optimized expense categories migration
     */
    protected function expenseCategoriesOptimized($batchSize)
    {
        $this->info('Migrating expense categories...');
        
        DB::connection('secondary')
            ->table('expenses_categories')
            ->orderBy('id')
            ->chunk($batchSize, function ($categories) {
                $insertData = [];
                
                foreach ($categories as $category) {
                    $insertData[] = [
                        'old_id' => $category->id,
                        'name' => $category->name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($insertData)) {
                    ExpenseCategory::insertOrIgnore($insertData);
                    $this->info('Processed ' . count($insertData) . ' expense categories');
                }
            });
    }

    /**
     * Optimized vouchers migration
     */
    protected function vouchersOptimized($batchSize)
    {
        $this->info('Migrating vouchers...');
        
        DB::connection('secondary')
            ->table('expense_vouchers')
            ->orderBy('id')
            ->chunk($batchSize, function ($vouchers) {
                $insertData = [];
                
                foreach ($vouchers as $voucher) {
                    $insertData[] = [
                        'old_id' => $voucher->id,
                        'exp_category_id' => $voucher->exp_category_id,
                        'service_order_id' => null,
                        'payed_to' => $this->getCachedUser($voucher->employee_id)?->id,
                        'payed_to_name' => $voucher->payed_to_others,
                        'amount' => $voucher->exp_amount_numbers ? 
                            ($voucher->exp_amount_numbers < 0 ? 
                                ($voucher->exp_amount_numbers * -1) : 
                                $voucher->exp_amount_numbers) : 0,
                        'created_at' => $voucher->created_on,
                        'updated_at' => $voucher->modified_on,
                    ];
                }
                
                if (!empty($insertData)) {
                    ExpenseVoucher::insertOrIgnore($insertData);
                    $this->info('Processed ' . count($insertData) . ' vouchers');
                }
            });
    }

    /**
     * Optimized expenses migration
     */
    protected function expensesOptimized($batchSize)
    {
        $this->info('Migrating expenses...');
        $batchId = Cache::get('migration_batch_id');
        $totalProcessed = 0;
        $totalSkipped = 0;
        $totalErrors = 0;
        $totalRegularAmount = 0;
        $totalInpatientAmount = 0;
        
        // Track processed IDs to detect potential duplicates
        $processedIds = [];
        
        // Regular expenses
        DB::connection('secondary')
            ->table('expenses')
            ->orderBy('id')
            ->chunk($batchSize, function ($expenses) use (&$totalProcessed, &$totalSkipped, &$totalErrors, &$totalRegularAmount, &$processedIds, $batchId) {
                $insertData = [];
                
                foreach ($expenses as $expense) {
                    try {
                        // Check for duplicates
                        $oldId = 'EXP-' . $expense->id;
                        if (in_array($oldId, $processedIds)) {
                            MigrationLog::logAction('expenses', MigrationLog::ACTION_DUPLICATED, [
                                'old_table' => 'expenses',
                                'old_record_id' => $expense->id,
                                'reason' => 'Duplicate expense ID detected during processing',
                                'batch_id' => $batchId,
                                'old_data' => (array)$expense
                            ]);
                            $totalSkipped++;
                            continue;
                        }
                        $processedIds[] = $oldId;

                        // Validate amount
                        $amount = $this->sanitizeNumericValue($expense->amount_received_num);
                        if ($amount <= 0) {
                            MigrationLog::logSkipped('expenses', 'expenses', $expense->id, 'Invalid or zero amount', (array)$expense);
                            $totalSkipped++;
                            continue;
                        }

                        $expenseCategory = ExpenseCategory::where('old_id', $expense->category_id)->first();
                        
                        $expenseData = [
                            'old_id' => $oldId,
                            'voucher_id' => $expense->voucher_id,
                            'exp_category_id' => $expenseCategory?->id,
                            'type' => $expense->payment_type,
                            'payed_to' => $this->getCachedUser($expense->payed_to)?->id,
                            'payed_to_name' => $expense->payment_reference,
                            'amount' => $amount,
                            'amount_alphabetical' => $expense->amount_received_words,
                            'created_at' => $expense->created_on,
                            'updated_at' => $expense->modified_on,
                        ];

                        $insertData[] = $expenseData;
                        $totalProcessed++;
                        $totalRegularAmount += $amount;

                        // Log successful processing
                        MigrationLog::logFinancial('expenses', 'expenses', $expense->id, 'expenses', null, 
                            $expense->amount_received_num, $amount, MigrationLog::ACTION_SUCCESS);

                    } catch (\Exception $e) {
                        MigrationLog::logError('expenses', 'expenses', $expense->id, $e->getMessage(), (array)$expense);
                        $totalErrors++;
                    }
                }
                
                if (!empty($insertData)) {
                    try {
                        Expense::insertOrIgnore($insertData);
                        $this->info('Processed ' . count($insertData) . ' regular expenses (Amount: ' . number_format(array_sum(array_column($insertData, 'amount')), 2) . ')');
                        
                        MigrationLog::logAction('expenses', MigrationLog::ACTION_SUCCESS, [
                            'reason' => 'Regular expenses batch inserted',
                            'old_table' => 'expenses',
                            'new_table' => 'expenses',
                            'batch_id' => $batchId,
                            'old_data' => [
                                'batch_count' => count($insertData),
                                'batch_amount' => array_sum(array_column($insertData, 'amount'))
                            ]
                        ]);

                    } catch (\Exception $e) {
                        MigrationLog::logError('expenses', 'expenses', null, $e->getMessage(), [
                            'batch_size' => count($insertData),
                            'batch_id' => $batchId
                        ]);
                        $this->error('Failed to insert regular expenses batch: ' . $e->getMessage());
                    }
                }
            });

        // Inpatient expenses
        DB::connection('secondary')
            ->table('inpatient_expense_transactions')
            ->orderBy('id')
            ->chunk($batchSize, function ($expenses) use (&$totalProcessed, &$totalSkipped, &$totalErrors, &$totalInpatientAmount, &$processedIds, $batchId) {
                $insertData = [];
                
                foreach ($expenses as $expense) {
                    try {
                        // Check for duplicates
                        $oldId = 'INP-EXP-' . $expense->id;
                        if (in_array($oldId, $processedIds)) {
                            MigrationLog::logAction('expenses', MigrationLog::ACTION_DUPLICATED, [
                                'old_table' => 'inpatient_expense_transactions',
                                'old_record_id' => $expense->id,
                                'reason' => 'Duplicate inpatient expense ID detected during processing',
                                'batch_id' => $batchId,
                                'old_data' => (array)$expense
                            ]);
                            $totalSkipped++;
                            continue;
                        }
                        $processedIds[] = $oldId;

                        // Validate amount
                        $amount = $this->sanitizeNumericValue($expense->amount_in_num);
                        if ($amount <= 0) {
                            MigrationLog::logSkipped('expenses', 'inpatient_expense_transactions', $expense->id, 'Invalid or zero amount', (array)$expense);
                            $totalSkipped++;
                            continue;
                        }

                        $expenseData = [
                            'old_id' => $oldId,
                            'type' => $expense->payment_type,
                            'payed_to' => $this->getCachedUser($expense->receaved_by)?->id,
                            'payed_to_name' => $expense->payment_refference,
                            'amount' => $amount,
                            'amount_alphabetical' => $expense->amount_in_figure,
                            'created_at' => $expense->created_on,
                            'updated_at' => $expense->modified_on,
                        ];

                        $insertData[] = $expenseData;
                        $totalProcessed++;
                        $totalInpatientAmount += $amount;

                        // Log successful processing
                        MigrationLog::logFinancial('expenses', 'inpatient_expense_transactions', $expense->id, 'expenses', null, 
                            $expense->amount_in_num, $amount, MigrationLog::ACTION_SUCCESS);

                    } catch (\Exception $e) {
                        MigrationLog::logError('expenses', 'inpatient_expense_transactions', $expense->id, $e->getMessage(), (array)$expense);
                        $totalErrors++;
                    }
                }
                
                if (!empty($insertData)) {
                    try {
                        Expense::insertOrIgnore($insertData);
                        $this->info('Processed ' . count($insertData) . ' inpatient expenses (Amount: ' . number_format(array_sum(array_column($insertData, 'amount')), 2) . ')');
                        
                        MigrationLog::logAction('expenses', MigrationLog::ACTION_SUCCESS, [
                            'reason' => 'Inpatient expenses batch inserted',
                            'old_table' => 'inpatient_expense_transactions',
                            'new_table' => 'expenses',
                            'batch_id' => $batchId,
                            'old_data' => [
                                'batch_count' => count($insertData),
                                'batch_amount' => array_sum(array_column($insertData, 'amount'))
                            ]
                        ]);

                    } catch (\Exception $e) {
                        MigrationLog::logError('expenses', 'inpatient_expense_transactions', null, $e->getMessage(), [
                            'batch_size' => count($insertData),
                            'batch_id' => $batchId
                        ]);
                        $this->error('Failed to insert inpatient expenses batch: ' . $e->getMessage());
                    }
                }
            });

        // Log final summary
        MigrationLog::logAction('expenses', MigrationLog::ACTION_SUCCESS, [
            'reason' => 'Expenses migration completed',
            'old_table' => 'expenses + inpatient_expense_transactions',
            'new_table' => 'expenses',
            'batch_id' => $batchId,
            'old_data' => [
                'total_processed' => $totalProcessed,
                'total_skipped' => $totalSkipped,
                'total_errors' => $totalErrors,
                'total_regular_amount' => $totalRegularAmount,
                'total_inpatient_amount' => $totalInpatientAmount,
                'total_amount' => $totalRegularAmount + $totalInpatientAmount
            ]
        ]);

        $this->info("✅ Expenses migration completed:");
        $this->info("   • Regular expenses: " . number_format($totalRegularAmount, 2));
        $this->info("   • Inpatient expenses: " . number_format($totalInpatientAmount, 2));
        $this->info("   • Total amount: " . number_format($totalRegularAmount + $totalInpatientAmount, 2));
        $this->info("   • Records: {$totalProcessed} processed, {$totalSkipped} skipped, {$totalErrors} errors");
    }

    /**
     * Optimized counter closing transactions migration
     */
    protected function counterClosingTransactionsOptimized($batchSize)
    {
        $this->info('Migrating counter closing transactions...');
        
        $statusObj = UpgradeProcess::firstOrCreate(
            ['name' => 'transaction_id'],
            ['value' => 0]
        );

        if ($statusObj->value === -1) {
            return;
        }

        $lastProcessedId = $statusObj->value;

        // Count records in both databases for progress tracking
        $oldTransactionCount = DB::connection('secondary')
            ->table('reception_counters_closings_transactions')
            ->count();

        $newTransactionCount = Transaction::count();

        $percentage = $oldTransactionCount > 0 ? 
            round(($newTransactionCount / $oldTransactionCount) * 100, 2) : 0;

        // Save progress percentage to UpgradeProcess
        $progressObj = UpgradeProcess::updateOrCreate(
            ['name' => 'transaction_migration_percentage'],
            ['value' => $percentage]
        );

        $this->info("Transaction migration progress: {$newTransactionCount}/{$oldTransactionCount} ({$percentage}%)");

        // Also update total counts for reference
        UpgradeProcess::updateOrCreate(
            ['name' => 'total_old_transactions'],
            ['value' => $oldTransactionCount]
        );

        UpgradeProcess::updateOrCreate(
            ['name' => 'total_new_transactions'],
            ['value' => $newTransactionCount]
        );

        // Get transactions with their elements in one query using JOIN
        $transactions = DB::connection('secondary')
            ->table('reception_counters_closings_transactions as t')
            ->leftJoin('reception_counters_closings_transaction_elements as e', 't.id', '=', 'e.id')
            ->where('t.id', '>', $lastProcessedId)
            ->orderBy('t.id')
            ->limit($batchSize)
            ->get([
                't.*',
                'e.id as element_id',
                'e.type as element_type',
                'e.amount as element_amount',
                'e.original_amount as element_original_amount',
                'e.doctor_id',
                'e.service_id',
                'e.department_transaction_id',
                'e.created_on as element_created_on',
                'e.modified_on as element_modified_on'
            ]);

        if ($transactions->isEmpty()) {
            $statusObj->value = -1; // -1 represents "finished"
            $statusObj->save();
            $this->info('Transaction migration completed');
            return;
        }

        // Group transactions by transaction ID
        $groupedTransactions = $transactions->groupBy('id');

        foreach ($groupedTransactions as $transactionId => $transactionGroup) {
            $transaction = $transactionGroup->first();

            // Create transaction first
            $isExpense = $transaction->income_or_expence !== 'INCOME';
            
            // Generate transaction number based on old transaction's creation date
            $createdAt = Carbon::parse($transaction->created_on);
            $year = $createdAt->format('Y');
            $month = $createdAt->format('m');
            $day = $createdAt->format('d');
            
            // Get count for that specific date to maintain unique numbering
            $existingCount = Transaction::where('tr_number', 'like', "TR/{$year}/{$month}/{$day}%")->count();
            $trNumber = "TR/{$year}/{$month}/{$day}/" . str_pad($existingCount + 1, 4, '0', STR_PAD_LEFT);
            
            $transactionData = [
                'old_id' => $transaction->id,
                'tr_number' => $trNumber,
                'closing_id' => $this->getCachedClosing($transaction->counter_id)?->id ?? null,
                'created_by' => $this->getCachedUser($transaction->user_id)?->id ?? null,
                'patient_id' => $this->getCachedPatient($transaction->patient_id)?->id ?? null,
                'type' => $this->mapTransactionType($transaction->type),
                'income_or_expense' => $transaction->income_or_expence === 'INCOME' ? 'INCOME' : 'EXPENSE',
                'amount' => $this->sanitizeTransactionAmount($transaction->amount, $isExpense),
                'orignal_amount' => $this->sanitizeTransactionAmount($transaction->orignal_amount, $isExpense),
                'customer_payed' => $isExpense ? 0 : $this->sanitizeNumericValue($transaction->customer_payed),
                'change' => $isExpense ? 0 : $this->sanitizeNumericValue($transaction->change),
                'edited_amount' => $this->sanitizeTransactionAmount($transaction->edited_amount, $isExpense),
                'created_at' => $transaction->created_on,
                'updated_at' => $transaction->modified_on,
            ];

            // Use insertGetId to get the new transaction ID
            $newTransactionId = Transaction::insertGetId($transactionData);

            // Prepare elements for this transaction
            $elementInserts = [];
            foreach ($transactionGroup as $element) {
                if (!$element->element_type || !$element->element_id) continue;

                $elementData = $this->prepareElementData($element, $transaction);
                
                if ($elementData) {
                    $elementData['transaction_id'] = $newTransactionId;
                    $elementData['closing_id'] = $transactionData['closing_id'];
                    $elementInserts[] = TransactionElement::create($elementData);
                }
            }

            // Bulk insert elements for this transaction
            // if (!empty($elementInserts)) {
            //     TransactionElement::create($elementInserts);
            // }

            $statusObj->value = $transactionId;
        }

        $statusObj->save();
        $this->info('Processed ' . count($groupedTransactions) . ' transactions');
    }

    /**
     * Map transaction type
     */
    protected function mapTransactionType($type)
    {
        return match($type) {
            'CARD', 'CREDITCARD' => 'CARD',
            'CHEQUE' => 'CHEQUE',
            default => 'CASH'
        };
    }

    /**
     * Prepare element data based on type
     */
    protected function prepareElementData($element, $transaction)
    {
        $isExpenseElement = in_array($element->element_type, ['EXP', 'VOUCHER-PAY', 'INPT-EXP']);
        
        $baseData = [
            'old_id' => $element->id,
            'closing_id' => $this->getCachedClosing($transaction->counter_id)?->id,
            'transaction_id' => null, // Will be set after transaction is created
            'created_by' => $this->getCachedUser($transaction->user_id)?->id,
            'amount' => $this->sanitizeTransactionAmount($element->element_amount, $isExpenseElement),
            'orignal_amount' => $this->sanitizeTransactionAmount($element->element_original_amount, $isExpenseElement),
            'customer_payed' => 0, // Always 0 for elements
            'change' => 0, // Always 0 for elements
            'edited_amount' => null,
            'service_order_id' => null,
            'created_at' => $element->element_created_on,
            'updated_at' => $element->element_modified_on,
        ];

        switch ($element->element_type) {
            case 'OPD':
            case 'INPT':
            case 'EMER':
            case 'DENTAL':
            case 'ULTRA':
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => $this->getCachedUser($element->doctor_id)?->id,
                    'patient_id' => $this->getCachedPatient($element->patient_id)?->id,
                    'service_id' => $this->getCachedService($element->service_id, $this->mapServiceType($element->element_type))?->id,
                    'service_recestation_id' => null,
                    'expense_id' => null,
                    'exp_voucher_id' => null,
                    'type' => $this->mapServiceType($element->element_type),
                ]);

            case 'RECES':
                $service = $this->getCachedRecestationService($element->service_id, $element->element_type);
                
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => null,
                    'patient_id' => $this->getCachedPatient($transaction->patient_id)?->id,
                    'service_id' => null,
                    'service_recestation_id' => $service?->id,
                    'expense_id' => null,
                    'exp_voucher_id' => null,
                    'type' => 'RECES-IND',
                ]);

            case 'EXP':
                $expense = $this->getCachedExpense('EXP-' . $element->department_transaction_id);
                return array_merge($baseData, [
                    'type' => 'EXP',
                    'income_or_expense' => 'EXPENSE',
                    'doctor_id' => null,
                    'patient_id' => null,
                    'service_id' => null,
                    'service_recestation_id' => null,
                    'expense_id' => $expense?->id,
                    'exp_voucher_id' => null,
                ]);

            case 'VOUCHER-PAY':
            case 'INPT-EXP':
                $expenseKey = $element->element_type === 'VOUCHER-PAY' ? 
                    'EXP-' . $element->department_transaction_id : 
                    'INP-EXP-' . $element->department_transaction_id;
                
                $expense = $this->getCachedExpense($expenseKey);
                $voucher = $expense ? ExpenseVoucher::find($expense->voucher_id) : null;
                
                return array_merge($baseData, [
                    'type' => $element->element_type,
                    'income_or_expense' => 'EXPENSE',
                    'doctor_id' => null,
                    'patient_id' => $element->element_type === 'INPT-EXP' ? 
                        $this->getCachedPatient($transaction->patient_id)?->id : null,
                    'service_id' => null,
                    'service_recestation_id' => null,
                    'expense_id' => $expense?->id,
                    'exp_voucher_id' => $voucher?->id,
                ]);
        }

        return null;
    }

    /**
     * Map service types
     */
    protected function mapServiceType($type)
    {
        return match($type) {
            'OPD' => 'OPD',
            'INPT' => 'IND',
            'EMER' => 'EMG',
            'DENTAL' => 'DNT',
            'ULTRA' => 'ULT',
            default => $type
        };
    }

    // Cache helper methods
    protected function getCachedUser($id)
    {
        return $this->userCache[$id] ?? null;
    }

    protected function getCachedPatient($id)
    {
        if (!$id) return null;
        
        if (!isset($this->patientCache[$id])) {
            $patient = Patient::find($id);
            if ($patient) {
                $this->patientCache[$id] = $patient;
            }
        }
        
        return $this->patientCache[$id] ?? null;
    }

    protected function getCachedReception($id)
    {
        return $this->receptionCache[$id] ?? null;
    }

    protected function getCachedClosing($id)
    {
        if (!$id || $id == 0) {
            return null;
        }
        
        if (!isset($this->closingCache[$id])) {
            // First check if already exists in new database
            $closingObj = Closing::where('id', $id)->first();
            
            if ($closingObj) {
                $this->closingCache[$id] = $closingObj;
                return $closingObj;
            }

            // If not found in new DB, get from old DB and create
            $closing = DB::connection('secondary')->table('reception_counters_closings')->find($id);
            
            if ($closing) {
                // Create the closing record in new database
                $countInMonth = Closing::whereYear('created_at', Carbon::parse($closing->created_on)->year)
                    ->whereMonth('created_at', Carbon::parse($closing->created_on)->month)
                    ->count();

                $ctNumber = 'CT/' . Carbon::parse($closing->created_on)->format('Y/m/') . str_pad($countInMonth + 1, 4, '0', STR_PAD_LEFT);

                $newClosing = Closing::create([
                    'id' => $closing->id,
                    'reception_id' => $this->getCachedReception($closing->reception_id)?->id ?? null,
                    'receptionist_id' => $this->getCachedUser($closing->user_id)?->id ?? null,
                    'ct_number' => $ctNumber,
                    'status' => $closing->status,
                    'opening_amount' => $closing->opening_amount,
                    'closing_amount' => $closing->closing_amount,
                    'closing_amount_cash' => $closing->closing_amount_cash,
                    'closing_amount_cheque' => 0,
                    'closing_amount_card' => ($closing->closing_amount_card ?? 0) + ($closing->closing_amount_creditcard ?? 0),
                    'expense_payed' => $closing->expense_payed,
                    'cash_recieving_time' => $closing->cash_recieving_time,
                    'created_at' => $closing->created_on,
                    'updated_at' => $closing->modified_on,
                ]);
                
                $this->closingCache[$id] = $newClosing;
                return $newClosing;
            }
        }
        
        return $this->closingCache[$id] ?? null;
    }

    protected function getCachedService($id, $type)
    {
        $key = "{$type}_{$id}";
        
        if (!isset($this->serviceCache[$key])) {
            // Get ServiceDepartment based on type
            $serviceDepartment = ServiceDepartment::where('slug', $type)->first();

            $service = Service::where('old_id', $id)
                ->where('service_department_id', $serviceDepartment?->id)
                ->first();

            if ($service) {
                $this->serviceCache[$key] = $service;
            }
        }
        
        return $this->serviceCache[$key] ?? null;
    }

    protected function getCachedRecestationService($id, $type)
    {
        $key = "{$type}_{$id}";
        
        if (!isset($this->serviceRecesitationCache[$key])) {
            // Get ServiceDepartment based on type
            $serviceDepartment = ServiceDepartment::where('slug', 'IND')->first();

            $service = ServiceRecestation::where('old_id', $id)
                ->where('service_department_id', $serviceDepartment?->id)
                ->first();

            if ($service) {
                $this->serviceRecesitationCache[$key] = $service;
            }
        }
        
        return $this->serviceRecesitationCache[$key] ?? null;
    }

    protected function getCachedExpense($id)
    {
        if (!isset($this->expenseCache[$id])) {
            $expense = Expense::where('old_id', $id)->first();
            if ($expense) {
                $this->expenseCache[$id] = $expense;
            }
        }
        
        return $this->expenseCache[$id] ?? null;
    }

    /**
     * Sanitize numeric values to prevent out-of-range errors
     */
    protected function sanitizeNumericValue($value)
    {
        if ($value === null) {
            return null;
        }

        // Convert to numeric
        $numericValue = is_numeric($value) ? (float)$value : 0;

        // Define reasonable business limits for a hospital system
        // Most transactions shouldn't exceed 1 million in any currency
        $maxReasonableValue = 1000000; // 1 million
        $minReasonableValue = -1000000;

        // Check for obviously corrupt data (2147483647 is max 32-bit int, likely corrupt)
        if ($numericValue >= 2147483647 || $numericValue <= -2147483648) {
            $this->warn("Detected corrupt max/min int value: {$numericValue}, setting to 0");
            return 0;
        }

        // Check for unreasonably large values that might be data corruption
        if ($numericValue > $maxReasonableValue) {
            $this->warn("Value {$numericValue} seems unreasonably large for hospital transaction, clamping to {$maxReasonableValue}");
            return $maxReasonableValue;
        }

        if ($numericValue < $minReasonableValue) {
            $this->warn("Value {$numericValue} seems unreasonably negative for hospital transaction, clamping to {$minReasonableValue}");
            return $minReasonableValue;
        }

        // Additional check for MySQL INT limit (just to be safe)
        $mysqlIntMax = 2147483647;
        $mysqlIntMin = -2147483648;

        if ($numericValue >= $mysqlIntMax) {
            $this->warn("Value {$numericValue} at MySQL int limit, setting to safe value");
            return $maxReasonableValue;
        }

        if ($numericValue <= $mysqlIntMin) {
            $this->warn("Value {$numericValue} at MySQL int limit, setting to safe value");
            return $minReasonableValue;
        }

        return $numericValue;
    }

    /**
     * Sanitize transaction amounts, handling negative values for expenses
     */
    protected function sanitizeTransactionAmount($value, $isExpense = false)
    {
        if ($value === null) {
            return null;
        }

        // Convert to numeric
        $numericValue = is_numeric($value) ? (float)$value : 0;

        // Handle corrupt data first
        if ($numericValue >= 2147483647 || $numericValue <= -2147483648) {
            $this->warn("Detected corrupt max/min int value: {$numericValue}, setting to 0");
            return 0;
        }

        // For expense transactions, old database stores negative values
        // Convert negative to positive for storage
        if ($isExpense && $numericValue < 0) {
            $numericValue = abs($numericValue);
        }

        // Apply business logic limits
        // $maxReasonableValue = 1000000; // 1 million
        
        // if ($numericValue > $maxReasonableValue) {
        //     $this->warn("Transaction amount {$numericValue} seems unreasonably large, clamping to {$maxReasonableValue}");
        //     return $maxReasonableValue;
        // }

        // For expenses, ensure positive values
        if ($isExpense && $numericValue < 0) {
            $this->warn("Expense transaction has negative amount {$numericValue}, converting to positive");
            return abs($numericValue);
        }

        return $numericValue;
    }


}