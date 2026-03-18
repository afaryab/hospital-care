<?php

namespace App\Console\Commands;

use App\Enum\CounterStatus;
use App\Enum\ServiceOrderStatus;
use App\Enum\TransactionElementType;
use App\Models\Closing;
use App\Models\UpgradeProcess;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;
use App\Models\Dentist;
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

class FetchOldHIMSOld extends Command
{
    public static $TOTAL_STEPS = 163;

    /**
     * The name and signature of the console command.
     */
    protected $signature = 'app:fetch-old-x {--step=} {--reset} {--batch-size=2000}';

    /**
     * The console command description.
     */
    protected $description = 'Optimized data migration from old schema database to new schema';

    // Cache collections for frequently accessed data
    protected $userCache = [];
    protected $patientCache = [];
    protected $serviceCache = [];
    protected $serviceRecesitationCache = [];
    protected $serviceOrderCache = [];
    protected $serviceDeptCache = [];
    protected $receptionCache = [];
    protected $closingCache = [];
    protected $expenseCache = [];
    protected $expenseCategoryCache = [];

    public function info($string, $verbosity = null)
    {
        parent::info($string, $verbosity);
        Log::info($string);
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {

        if(env('ENABLE_OLD_SYNC', false) !== 'hims') {
            return 1;
        }

        $this->info('Starting optimized fetch-old command execution.');
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
            case 81:
            case 82:
            case 83:
            case 84:
            case 85:
            case 86:
            case 87:
            case 88:
            case 89:
            case 90:
            case 91:
                $month = $currentStep - 80;
                $this->counterClosingTransactionsOptimized(2020, $month, $batchSize);
                break;
            case 92:
            case 93:
            case 94:
            case 95:
            case 96:
            case 97:
            case 98:
            case 99:
            case 100:
            case 101:
            case 102:
            case 103:
                $month = $currentStep - 92;
                $this->counterClosingTransactionsOptimized(2021, $month, $batchSize);
                break;
            case 104:
            case 105:
            case 106:
            case 107:
            case 108:
            case 109:
            case 110:
            case 111:
            case 112:
            case 113:
            case 114:
            case 115:
                $month = $currentStep - 104;
                $this->counterClosingTransactionsOptimized(2022, $month, $batchSize);
                break;
            case 116:
            case 117:
            case 118:
            case 119:
            case 120:
            case 121:
            case 122:
            case 123:
            case 124:
            case 125:
            case 126:
            case 127:
                $month = $currentStep - 116;
                $this->counterClosingTransactionsOptimized(2023, $month, $batchSize);
                break;
            case 128:
            case 129:
            case 130:
            case 131:
            case 132:
            case 133:
            case 134:
            case 135:
            case 136:
            case 137:
            case 138:
            case 139:
                $month = $currentStep - 128;
                $this->counterClosingTransactionsOptimized(2024, $month, $batchSize);
                break;
            case 140:
            case 141:
            case 142:
            case 143:
            case 144:
            case 145:
            case 146:
            case 147:
            case 148:
            case 149:
            case 150:
            case 151:
                $month = $currentStep - 140;
                $this->counterClosingTransactionsOptimized(2025, $month, $batchSize);
                break;
            case 152:
            case 153:
            case 154:
            case 155:
            case 156:
            case 157:
            case 158:
            case 159:
            case 160:
            case 161:
            case 162:
            case 163:
                $month = $currentStep - 152;
                $this->counterClosingTransactionsOptimized(2026, $month, $batchSize);
                break;
            default:
                break;

            // if($currentStep < 200) {
            //     $this->info('Step ' . $currentStep . ' completed. Please run the command again to execute the next step.');
            // } else if($currentStep > self::$TOTAL_STEPS) {
            //     $this->info('No more steps to execute.');
            // }
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
                        'created_at' => $image->created_on,
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
                            'created_at' => $service->created_on,
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
                                'created_at' => $service->created_on,
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
                        'created_at' => $reception->created_on,
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
                    'contact' => $this->validatePhoneNumber($patient->patient_contact_mobile) ? $this->formatePhoneNumber($patient->patient_contact_mobile) : null,
                    'cnic' => $this->validateCnic($patient->patient_cnic) ? $this->formateCnic($patient->patient_cnic) : null,
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
                    
    protected function validatePhoneNumber($number)
    {
        $number = preg_replace('/\D/', '', $number);
        if (Str::startsWith($number, '92') && strlen($number) == 12) {
            return true;
        } elseif (Str::startsWith($number, '0') && strlen($number) == 11) {
            return true;
        } elseif (Str::startsWith($number, '3') && strlen($number) == 10) {
            return true;
        }
        return false;
    }
    // Expected formats: +92-XXXXXXXXXX
    protected function formatePhoneNumber($number)
    {
        $number = preg_replace('/\D/', '', $number);
        if (Str::startsWith($number, '92')) {
            return '+92-' . substr($number, 2);
        } elseif (Str::startsWith($number, '0')) {
            return '+92-' . substr($number, 1);
        } elseif (Str::startsWith($number, '3') && strlen($number) == 10) {
            return '+92-' . $number;
        }
        return $number; // Return as is if it doesn't match expected patterns
    }

    protected function validateCnic($cnic)
    {
        $cnic = preg_replace('/\D/', '', $cnic);
        return strlen($cnic) == 13;
    }
    // Expected format: XXXXX-XXXXXXX-X
    protected function formateCnic($cnic)
    {
        $cnic = preg_replace('/\D/', '', $cnic);
        if (strlen($cnic) == 13) {
            return substr($cnic, 0, 5) . '-' . substr($cnic, 5, 7) . '-' . substr($cnic, 12);
        }
        return $cnic; // Return as is if it doesn't match expected patterns
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
                        'type' => $category->type,
                        'pay_doc' => $category->pay_doc,
                        'pay_others' => $category->pay_others,
                        'pay_users' => $category->pay_users,
                        'created_at' => $category->created_on,
                        'updated_at' => now(),
                    ];
                }
                
                if (!empty($insertData)) {
                    $cat = ExpenseCategory::insertOrIgnore($insertData);
                    $this->expenseCategoryCache[$category->id] = $cat;
                    $this->info('Processed ' . count($insertData) . ' expense categories');
                }
            });
    }

    protected function counterClosingTransactionsOptimized($year, $month, $batchSize){

        $this->info('Migrating counter closing transactions...');

        $year = 2025;

        // Get transactions with their elements in one query using JOIN
        $transactions = DB::connection('secondary')
            ->table('reception_counters_closings_transactions as t')
            ->leftJoin('reception_counters_closings_transaction_elements as e', 't.id', '=', 'e.closing_transaction_id')
            ->leftJoin('patients as p', 't.patient_id', '=', 'p.id')
            // ->whereYear('t.created_on', $year)
            // ->whereMonth('t.created_on', $month)
            ->where('t.id', 3334) // INPT-EXP
            // ->where('t.id', 84) // INP
            // ->where('t.id', 30686) // PETTY CASH
            // ->where('t.id', 413868) // VOUCHER PAYMENT
            ->orderBy('t.id')
            ->select([
                't.*',
                'e.id as element_id',
                'e.type as element_type',
                'e.amount as element_amount',
                'e.original_amount as element_original_amount',
                'e.doctor_id as element_doctor_id',
                'e.service_id as element_service_id',
                'e.department_transaction_id as element_department_transaction_id',
                'e.created_on as element_created_on',
                'e.modified_on as element_modified_on',
                'p.id as patient_id',
                'p.pateint_name as patient_name',
                'p.gender as patient_gender',
                'p.age_group as patient_age_group',
                'p.age_days as patient_age_days',
                'p.age_dob as patient_age_dob',
                'p.patient_address as patient_address',
                'p.guardian as patient_guardian',
                'p.relation as patient_relation',
                'p.patient_contact_mobile as patient_contact',
                'p.patient_cnic as patient_cnic',
                'p.patient_email as patient_email',
                'p.patient_profession as patient_profession',
                'p.created_on as patient_created_on',
                'p.modified_on as patient_modified_on',
            ])->get();

            $groupedTransactions = $transactions->groupBy('id');

            foreach ($groupedTransactions as $transactionId => $transactionGroup) {

                $transaction = $transactionGroup->first();

                $patientData = [
                    'old_id' => $transaction->patient_id,
                    'name' => $transaction->patient_name,
                    'gender' => $transaction->patient_gender,
                    'age_group' => $transaction->patient_age_group,
                    'age_days' => $transaction->patient_age_days,
                    'age_dob' => $transaction->patient_age_dob,
                    'address' => $transaction->patient_address,
                    'guardian' => $transaction->patient_guardian,
                    'relation' => $transaction->patient_relation,
                    'contact' => $this->validatePhoneNumber($transaction->patient_contact) ? $this->formatePhoneNumber($transaction->patient_contact) : null,
                    'cnic' => $this->validateCnic($transaction->patient_cnic) ? $this->formateCnic($transaction->patient_cnic) : null,
                    'email' => $transaction->patient_email,
                    'profession' => $transaction->patient_profession,
                    'created_at' => $transaction->patient_created_on,
                    'updated_at' => $transaction->patient_modified_on,
                ];

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
                    'amount' => $this->sanitizeTransactionAmount($transaction->amount, $isExpense),
                    'orignal_amount' => $this->sanitizeTransactionAmount($transaction->orignal_amount, $isExpense),
                    'customer_payed' => $isExpense ? 0 : $this->sanitizeNumericValue($transaction->customer_payed),
                    'change' => $isExpense ? 0 : $this->sanitizeNumericValue($transaction->change),
                    'edited_amount' => $this->sanitizeTransactionAmount($transaction->edited_amount, $isExpense),
                    'created_at' => $transaction->created_on,
                    'updated_at' => $transaction->modified_on,
                ];

                $transactionData['type'] = $transaction->income_or_expence === 'INCOME' ? $this->mapTransactionType($transaction->type) : 'CASH';
                $transactionData['income_or_expense'] = $transaction->income_or_expence === 'INCOME' ? 'INCOME' : 'EXPENSE';
                $transactionData['department'] = $transaction->element_type;

                $transactionData['patient'] = $patientData;

                foreach ($transactionGroup as $element) {
                    $this->info("Processing transaction ID: {$transaction->id} with element ID: {$element->element_id} and type {$transactionData['type']} and amount {$transactionData['amount']} and INC/EXP: {$transactionData['income_or_expense']}");
                    
                    $transactionData['elements'][$element->element_id] = $this->prepareElementData($element, $transaction);

                    
                }

                dd($transactionData);

                $patientProcessed = Patient::firstOrCreate(
                    ['old_id' => $transactionData['patient']['old_id']],
                    [
                        'name' => $transactionData['patient']['name'],
                        'gender' => $transactionData['patient']['gender'],
                        'age_group' => $transactionData['patient']['age_group'],
                        'age_days' => $transactionData['patient']['age_days'],
                        'age_dob' => $transactionData['patient']['age_dob'],
                        'address' => $transactionData['patient']['address'],
                        'guardian' => $transactionData['patient']['guardian'],
                        'relation' => $transactionData['patient']['relation'],
                        'contact' => $transactionData['patient']['contact'],
                        'cnic' => $transactionData['patient']['cnic'],
                        'email' => $transactionData['patient']['email'],
                        'profession' => $transactionData['patient']['profession'],
                        'created_at' => $transactionData['patient']['created_at'],
                        'updated_at' => $transactionData['patient']['updated_at'],
                    ]
                );


                $transactionProcessed = Transaction::firstOrCreate(
                    ['old_id' => $transactionData['old_id']],
                    [
                        'tr_number' => $transactionData['tr_number'],
                        'closing_id' => $transactionData['closing_id'],
                        'created_by' => $transactionData['created_by'],
                        'amount' => $transactionData['amount'],
                        'orignal_amount' => $transactionData['orignal_amount'],
                        'customer_payed' => $transactionData['customer_payed'],
                        'change' => $transactionData['change'],
                        'edited_amount' => $transactionData['edited_amount'],
                        'type' => $transactionData['type'],
                        'income_or_expense' => $transactionData['income_or_expense'],
                        'department_id' => $transactionData['department'],
                        'created_at' => $transactionData['created_at'],
                        'updated_at' => $transactionData['updated_at'],
                    ]
                );

                foreach($transactionData['elements'] as $elementId => $elementData){
                    $this->info("Processing element ID: {$elementId} for transaction ID: {$transaction->id} with service ID: {$elementData['service_id']} and doctor ID: {$elementData['doctor_id']}");

                    if(array_key_exists('service_order_id', $elementData)){
                        $this->info("Element ID: {$elementId} has service_order_id: {$elementData['service_order_id']}");

                        ServiceOrder::firstOrCreate(
                            ['old_id' => $elementData['service_order_id']],
                            [
                                'transaction_id' => $transactionProcessed->id,
                                'service_id' => $elementData['service_id'],
                                'doctor_id' => $elementData['doctor_id'],
                                'department_transaction_id' => $elementData['department_transaction_id'],
                                'created_at' => $elementData['created_at'],
                                'updated_at' => $elementData['updated_at'],
                            ]
                        );


                    } else {
                        $this->info("Element ID: {$elementId} does NOT have a service_order_id");
                    }



                    $transactionElement = TransactionElement::firstOrCreate(
                        ['old_id' => $elementId],
                        [
                            'transaction_id' => $transactionProcessed->id,
                            'type' => $elementData['type'],
                            'amount' => $elementData['amount'],
                            'original_amount' => $elementData['original_amount'],
                            'doctor_id' => $elementData['doctor_id'],
                            'service_id' => $elementData['service_id'],
                            'department_transaction_id' => $elementData['department_transaction_id'],
                            'created_at' => $elementData['created_at'],
                            'updated_at' => $elementData['updated_at'],
                        ]
                    );
                }
                
            }

        




    }

    /**
     * Optimized counter closing transactions migration
     */
    protected function counterClosingTransactionsOptimizedx($batchSize)
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
                'type' => $transaction->income_or_expence === 'INCOME' ? $this->mapTransactionType($transaction->type) : 'CASH',
                'income_or_expense' => $transaction->income_or_expence === 'INCOME' ? 'INCOME' : 'EXPENSE',
                'department_id' => $this->mapDepartmentId($transaction->department_id),

                'amount' => $this->sanitizeTransactionAmount($transaction->amount, $isExpense),
                'orignal_amount' => $this->sanitizeTransactionAmount($transaction->orignal_amount, $isExpense),
                'customer_payed' => $isExpense ? 0 : $this->sanitizeNumericValue($transaction->customer_payed),
                'change' => $isExpense ? 0 : $this->sanitizeNumericValue($transaction->change),
                'edited_amount' => $this->sanitizeTransactionAmount($transaction->edited_amount, $isExpense),
                'created_at' => $transaction->created_on,
                'updated_at' => $transaction->modified_on,
            ];

            $this->info("Processing transaction ID: {$transaction->id} with " . count($transactionGroup) . " elements and type {$transactionData['type']} and amount {$transactionData['amount']} and INC/EXP: {$transactionData['income_or_expense']}");

            // If is expense get expense record from secondary database and check if category is voucher pay or not, if voucher pay then set type as VOUCHER-PAY otherwise EXP
            if ($isExpense) {

                // Expense transactions have only one element which contains the expense details, so we can directly use the first element for this.
                $trE = $transactionGroup->first();

                if($trE->type === 'INPT-EXP') {
                    $this->info("Processing INPT-EXP transaction ID: {$transaction->id}");

                    $inPatientFileExpense = DB::connection('secondary')->table('inpatient_file_expenses')->where('id', $trE->department_transaction_id)->first();
                    if ($inPatientFileExpense) {
                        $transactionData['notes'] = $inPatientFileExpense->payment_reference;
                    }
                    

                }

                $expense = DB::connection('secondary')->table('expenses')->where('id', $trE->department_transaction_id)->first();
                
                $transactionData['expense_category_id'] = $expense ? ($this->getCachedExpenseCategory($expense->category_id)?->id ?? null) : null;
                $transactionData['notes'] = $expense ? $expense->payment_reference : null;

                $isVoucherPay = $expense->voucher_id ? true : false;

                // If Voucher Pay get voucher record from seconday database and create it here.
                if ($isVoucherPay) {
                    $voucher = DB::connection('secondary')->table('expense_vouchers')->where('id', $expense->voucher_id)->first();

                    if ($voucher) {
                        $voucherData = [
                            'old_id' => $voucher->id,
                            'exp_category_id' => $this->getCachedExpenseCategory($voucher->exp_category_id)?->id,
                            'service_order_id' => null,
                            'payed_to' => $this->getCachedUser($voucher->employee_id)?->id ?? null,
                            'payed_to_name' => $voucher->payed_to_others,
                            'amount' => $this->sanitizeTransactionAmount($voucher->exp_amount_numbers, true),
                            'notes' => $voucher->expense_notes,
                            'created_at' => $voucher->created_on,
                            'updated_at' => $voucher->modified_on,
                            'vc_number' => ExpenseVoucher::generateExpenseVoucherNumber()
                        ];

                        $newVoucherId = ExpenseVoucher::insertGetId($voucherData);
                        $transactionData['type'] = 'VOUCHER-PAY';
                        $transactionData['exp_voucher_id'] = $newVoucherId;
                    }
                } else {
                    $transactionData['type'] = 'EXP';
                }
                
            }

            // Use insertGetId to get the new transaction ID
            $newTransactionId = Transaction::insertGetId($transactionData);

            $this->info("Processing transaction ID: {$transaction->id} => New ID: {$newTransactionId} with " . count($transactionGroup) . " elements");

            // Prepare elements for this transaction
            $elementInserts = [];
            foreach ($transactionGroup as $element) {
                if (!$element->element_type || !$element->element_id) continue;

                $elementData = $this->prepareElementData($element, $transaction);
                
                if ($elementData) {
                    $elementData['transaction_id'] = $newTransactionId;
                    $elementData['closing_id'] = $transactionData['closing_id'];

                    if($isExpense) {
                        $elementData['expense_category_id'] = $transactionData['expense_category_id'];
                        $elementData['notes'] = $transactionData['notes'];
                        $elementData['exp_voucher_id'] = $transactionData['exp_voucher_id'] ?? null;
                    }

                    $elementData['income_or_expense'] = $transaction->income_or_expence;


                    $elementInserts[] = $te = TransactionElement::createQuietly($elementData);

                    // If Income and department is inpatient and $element->department_transaction_id is present, link it to the service order

                    if ($transaction->type === 'INCOME' && $element->department_transaction_id && in_array($element->element_type, ['INPT'])) {
                    
                        $this->info("Processing service order for transaction element ID: {$element->element_id}, department transaction ID: {$element->department_transaction_id}");

                        $InpFile = DB::connection('secondary')->table('inpatient_file')
                            ->select('*')
                            ->where('id', $element->department_transaction_id)
                            ->get();
                        if ($InpFile->isNotEmpty()) {
                            $InpFile = $InpFile->first();

                            $s = ServiceOrder::where('type', 'IND')->where('created_at', '>=', Carbon::now()->startOfMonth())
                                ->where('created_at', '<=', Carbon::now()->endOfMonth())
                                ->count();

                            $s = $s + 1;

                            $serviceOrder = ServiceOrder::create([
                                'so_number' => 'SO/' . Carbon::parse($element->element_created_on)->format('Ymd') . '/' . str_pad($s, 4, '0', STR_PAD_LEFT),
                                'so_short' => 'INPT/' . $te->id,
                                'transaction_element_id' => $te->id,
                                'patient_id' => $this->getCachedPatient($transaction->patient_id)?->id,
                                'service_id' => $this->getCachedService($element->service_id, $this->mapServiceType($element->element_type))?->id,
                                'doctor_id' => $this->getCachedUser($element->doctor_id)?->id,
                                'status' => $InpFile->status,
                                'created_at' => $element->element_created_on,
                                'updated_at' => $element->element_modified_on,
                            ]);
                    }


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
            'old_id' => $element->element_id,
            'closing_id' => $this->getCachedClosing($transaction->counter_id)?->id,
            'created_by' => $this->getCachedUser($transaction->user_id)?->id,
            'amount' => $this->sanitizeTransactionAmount($element->element_amount, $isExpenseElement),
            'orignal_amount' => $this->sanitizeTransactionAmount($element->element_original_amount, $isExpenseElement),
            'created_at' => $element->element_created_on,
            'updated_at' => $element->element_modified_on,
        ];

        switch ($element->element_type) {
            case 'EMER':
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => $this->getCachedUser($element->doctor_id)?->id,
                    'patient_id' => $this->getCachedPatient($element->patient_id)?->id,
                    'service_id' => $this->getCachedService($element->service_id, $this->mapServiceType($element->element_type))?->id,
                    'type' => $this->mapServiceType($element->element_type),
                ]);
            case 'OPD':
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => $this->getCachedUser($element->doctor_id)?->id,
                    'patient_id' => $this->getCachedPatient($element->patient_id)?->id,
                    'service_id' => $this->getCachedService($element->service_id, $this->mapServiceType($element->element_type))?->id,
                    'type' => $this->mapServiceType($element->element_type),
                ]);
            case 'DENTAL':
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => $this->getCachedUser($element->doctor_id)?->id,
                    'patient_id' => $this->getCachedPatient($element->patient_id)?->id,
                    'service_id' => $this->getCachedService($element->service_id, $this->mapServiceType($element->element_type))?->id,
                    'type' => $this->mapServiceType($element->element_type),
                ]);
            case 'ULTRA':
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => $this->getCachedUser($element->doctor_id)?->id,
                    'patient_id' => $this->getCachedPatient($element->patient_id)?->id,
                    'service_id' => $this->getCachedService($element->service_id, $this->mapServiceType($element->element_type))?->id,
                    'type' => $this->mapServiceType($element->element_type),
                ]);
            case 'INPT':
                $inpatient = DB::connection('secondary')
                    ->table('inpatient_transactions as inpatient_transactions')
                    ->where('inpatient_transactions.id', $element->element_department_transaction_id)
                    ->leftJoin('inpatient_file', 'inpatient_transactions.file_id', '=', 'inpatient_file.id')
                    ->select([
                        'inpatient_transactions.*',
                        'inpatient_file.id as inpatient_file_old_id',
                        'inpatient_file.panel_id as inpatient_file_panel_id',
                        'inpatient_file.treatment_by as inpatient_file_treatment_by',
                        'inpatient_file.patient_id as inpatient_file_patient_id',
                        'inpatient_file.inpatient_patient_id as inpatient_file_inpatient_patient_id',
                        'inpatient_file.status as inpatient_file_status',
                        'inpatient_file.patient_discomfort as inpatient_file_patient_discomfort',
                        'inpatient_file.patient_bleed_excess as inpatient_file_patient_bleed_excess',
                        'inpatient_file.already_medication as inpatient_file_already_medication',
                        'inpatient_file.patient_smoker as inpatient_file_patient_smoker',
                        'inpatient_file.patient_smoking_frequency as inpatient_file_patient_smoking_frequency',
                        'inpatient_file.is_diabetic as inpatient_file_is_diabetic',
                        'inpatient_file.tuberculosis as inpatient_file_tuberculosis',
                        'inpatient_file.hepatitis as inpatient_file_hepatitis',
                        'inpatient_file.epilepsy as inpatient_file_epilepsy',
                        'inpatient_file.rheumatic as inpatient_file_rheumatic',
                        'inpatient_file.hiv as inpatient_file_hiv',
                        'inpatient_file.is_heart_patient as inpatient_file_is_heart_patient',
                        'inpatient_file.is_allergietic as inpatient_file_is_allergietic',
                        'inpatient_file.prefer_anesthetic as inpatient_file_prefer_anesthetic',
                        'inpatient_file.is_pregnant as inpatient_file_is_pregnant',
                        'inpatient_file.patient_discomfirt_start as inpatient_file_patient_discomfirt_start',
                        'inpatient_file.patient_is_first_visit as inpatient_file_patient_is_first_visit',
                        'inpatient_file.patient_last_visit as inpatient_file_patient_last_visit',
                        'inpatient_file.patient_last_visit_process as inpatient_file_patient_last_visit_process',
                        'inpatient_file.patient_physician as inpatient_file_patient_physician',
                        'inpatient_file.patient_physician_phone as inpatient_file_patient_physician_phone',
                        'inpatient_file.patient_last_examination as inpatient_file_patient_last_examination',
                        'inpatient_file.patient_under_medical as inpatient_file_patient_under_medical',
                        'inpatient_file.service_id as inpatient_file_service_id',
                        'inpatient_file.service_name as inpatient_file_service_name',
                        'inpatient_file.room_id as inpatient_file_room_id',
                        'inpatient_file.room_name as inpatient_file_room_name',
                        'inpatient_file.panel_name as inpatient_file_panel_name',
                        'inpatient_file.file_orignal_charges as inpatient_file_file_orignal_charges',
                        'inpatient_file.file_charges as inpatient_file_file_charges',
                        'inpatient_file.declared_loss as inpatient_file_declared_loss',
                        'inpatient_file.declared_loss_by as inpatient_file_declared_loss_by',
                        'inpatient_file.file_charges_paid as inpatient_file_file_charges_paid',
                        'inpatient_file.open_on as inpatient_file_open_on',
                        'inpatient_file.closed_on as inpatient_file_closed_on',
                        'inpatient_file.will_occure_on as inpatient_file_will_occure_on',
                        'inpatient_file.is_visiting as inpatient_file_is_visiting',
                        'inpatient_file.modified_on as inpatient_file_modified_on',
                        'inpatient_file.created_on as inpatient_file_created_on',

                    ])
                    ->first();
                    
                return array_merge($baseData, [
                    'income_or_expense' => 'INCOME',
                    'doctor_id' => $this->getCachedUser($inpatient->inpatient_file_treatment_by)?->id,
                    'patient_id' => $this->getCachedPatient($inpatient->inpatient_file_patient_id)?->id,
                    'service_id' => $this->getCachedService($inpatient->inpatient_file_service_id, $this->mapServiceType($element->element_type))?->id,
                    'service_recestation_id' => null,
                    'expense_id' => null,
                    'exp_voucher_id' => null,
                    'type' => $this->mapServiceType($element->element_type),
                    'service_order' => $this->prepareSericeInpatientOrder($inpatient),
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
            case 'INPT-EXP':
                $inpatientExpense = DB::connection('secondary')
                                        ->table('inpatient_expense_transactions as inpatient_expense_transactions')
                                        ->where('inpatient_expense_transactions.id', $element->element_department_transaction_id)
                                        ->leftJoin('inpatient_file', 'inpatient_expense_transactions.file_id', '=', 'inpatient_file.id')
                                        ->select([
                                            'inpatient_expense_transactions.*',
                                            'inpatient_file.id as inpatient_file_old_id',
                                            'inpatient_file.panel_id as inpatient_file_panel_id',
                                            'inpatient_file.treatment_by as inpatient_file_treatment_by',
                                            'inpatient_file.patient_id as inpatient_file_patient_id',
                                            'inpatient_file.inpatient_patient_id as inpatient_file_inpatient_patient_id',
                                            'inpatient_file.status as inpatient_file_status',
                                            'inpatient_file.patient_discomfort as inpatient_file_patient_discomfort',
                                            'inpatient_file.patient_bleed_excess as inpatient_file_patient_bleed_excess',
                                            'inpatient_file.already_medication as inpatient_file_already_medication',
                                            'inpatient_file.patient_smoker as inpatient_file_patient_smoker',
                                            'inpatient_file.patient_smoking_frequency as inpatient_file_patient_smoking_frequency',
                                            'inpatient_file.is_diabetic as inpatient_file_is_diabetic',
                                            'inpatient_file.tuberculosis as inpatient_file_tuberculosis',
                                            'inpatient_file.hepatitis as inpatient_file_hepatitis',
                                            'inpatient_file.epilepsy as inpatient_file_epilepsy',
                                            'inpatient_file.rheumatic as inpatient_file_rheumatic',
                                            'inpatient_file.hiv as inpatient_file_hiv',
                                            'inpatient_file.is_heart_patient as inpatient_file_is_heart_patient',
                                            'inpatient_file.is_allergietic as inpatient_file_is_allergietic',
                                            'inpatient_file.prefer_anesthetic as inpatient_file_prefer_anesthetic',
                                            'inpatient_file.is_pregnant as inpatient_file_is_pregnant',
                                            'inpatient_file.patient_discomfirt_start as inpatient_file_patient_discomfirt_start',
                                            'inpatient_file.patient_is_first_visit as inpatient_file_patient_is_first_visit',
                                            'inpatient_file.patient_last_visit as inpatient_file_patient_last_visit',
                                            'inpatient_file.patient_last_visit_process as inpatient_file_patient_last_visit_process',
                                            'inpatient_file.patient_physician as inpatient_file_patient_physician',
                                            'inpatient_file.patient_physician_phone as inpatient_file_patient_physician_phone',
                                            'inpatient_file.patient_last_examination as inpatient_file_patient_last_examination',
                                            'inpatient_file.patient_under_medical as inpatient_file_patient_under_medical',
                                            'inpatient_file.service_id as inpatient_file_service_id',
                                            'inpatient_file.service_name as inpatient_file_service_name',
                                            'inpatient_file.room_id as inpatient_file_room_id',
                                            'inpatient_file.room_name as inpatient_file_room_name',
                                            'inpatient_file.panel_name as inpatient_file_panel_name',
                                            'inpatient_file.file_orignal_charges as inpatient_file_file_orignal_charges',
                                            'inpatient_file.file_charges as inpatient_file_file_charges',
                                            'inpatient_file.declared_loss as inpatient_file_declared_loss',
                                            'inpatient_file.declared_loss_by as inpatient_file_declared_loss_by',
                                            'inpatient_file.file_charges_paid as inpatient_file_file_charges_paid',
                                            'inpatient_file.open_on as inpatient_file_open_on',
                                            'inpatient_file.closed_on as inpatient_file_closed_on',
                                            'inpatient_file.will_occure_on as inpatient_file_will_occure_on',
                                            'inpatient_file.is_visiting as inpatient_file_is_visiting',
                                            'inpatient_file.modified_on as inpatient_file_modified_on',
                                            'inpatient_file.created_on as inpatient_file_created_on',

                                        ])
                                        ->first();
                
                return array_merge($baseData, [
                    'income_or_expense' => 'EXPENSE',
                    'doctor_id' => $inpatientExpense ? $this->getCachedUser($inpatientExpense->inpatient_file_treatment_by)?->id : null,
                    'patient_id' => $inpatientExpense ? $this->getCachedPatient($inpatientExpense->inpatient_file_patient_id)?->id : null,
                    'service_id' => $this->getCachedService($inpatientExpense->inpatient_file_service_id, 'IND')?->id,
                    'type' => 'INPT-EXP',
                    'service_order' => $this->prepareSericeInpatientOrder($inpatientExpense),
                ]);
            case 'EXP':
                
                $expense = DB::connection('secondary')->table('expenses')->where('id', $element->element_department_transaction_id)->first();
                return array_merge($baseData, [
                    'income_or_expense' => 'EXPENSE',
                    'type' => $this->mapServiceType($element->element_type),
                    'expense' => $expense ? $this->parseExpense($expense) : null,
                ]);
            case 'VOUCHER-PAY':
                $expense = DB::connection('secondary')
                    ->table('expenses')
                    ->where('expenses.id', $element->element_department_transaction_id)
                    ->leftJoin('expense_vouchers', 'expenses.voucher_id', '=', 'expense_vouchers.id')
                    ->select([
                        'expenses.*',
                        'expense_vouchers.id as voucher_id',
                        'expense_vouchers.exp_category_id as voucher_exp_category_id',
                        'expense_vouchers.inpatient_file_id as voucher_inpatient_file_id',
                        'expense_vouchers.exp_amount_numbers as voucher_exp_amount_numbers',
                        'expense_vouchers.exp_amount_words as voucher_exp_amount_words',
                        'expense_vouchers.payed_to_employee as voucher_payed_to_employee',
                        'expense_vouchers.payed_to_others as voucher_payed_to_others',
                        'expense_vouchers.employee_id as voucher_employee_id',
                        'expense_vouchers.expense_notes as voucher_expense_notes',
                        'expense_vouchers.created_on as voucher_created_on',
                        'expense_vouchers.modified_on as voucher_modified_on',
                    ])
                    ->first();
                return array_merge($baseData, [
                    'income_or_expense' => 'EXPENSE',
                    'type' => $this->mapServiceType($element->element_type),
                    'expense' => $expense ? $this->parseExpense($expense) : null,
                    'voucher' => $expense ? $this->parseExpenseElementVoucher($expense) : null,
                ]);

        }

        return null;
    }

    protected function parseExpense($expense){
        return [
            'old_id' => $expense->id,
            'category_id' => $expense->category_id,
            'voucher_id' => $expense->voucher_id,
            'payment_reference' => $expense->payment_reference,
            'created_on' => $expense->created_on,
            'modified_on' => $expense->modified_on
        ];
    }

    protected function parseExpenseElementVoucher($expense){
        return [
            'old_id' => $expense->voucher_id,
            'exp_category_id' => $expense->voucher_exp_category_id,
            'inpatient_file_id' => $expense->voucher_inpatient_file_id,
            'exp_amount_numbers' => $expense->voucher_exp_amount_numbers,
            'exp_amount_words' => $expense->voucher_exp_amount_words,
            'payed_to_employee' => $expense->voucher_payed_to_employee,
            'payed_to_others' => $expense->voucher_payed_to_others,
            'employee_id' => $expense->voucher_employee_id,
            'expense_notes' => $expense->voucher_expense_notes,
            'created_on' => $expense->voucher_created_on,
            'modified_on' => $expense->voucher_modified_on
        ];
    }


    protected function prepareSericeInpatientOrder($element)
    {
        return [
            'old_id' => $element->inpatient_file_old_id,
            'inpatient_file_panel_id' => $element->inpatient_file_panel_id,
            'inpatient_file_treatment_by' => $element->inpatient_file_treatment_by,
            'inpatient_file_patient_id' => $element->inpatient_file_patient_id,
            'inpatient_file_inpatient_patient_id' => $element->inpatient_file_inpatient_patient_id,
            'inpatient_file_status' => $element->inpatient_file_status,
            'inpatient_file_patient_discomfort' => $element->inpatient_file_patient_discomfort,
            'inpatient_file_patient_bleed_excess' => $element->inpatient_file_patient_bleed_excess,
            'inpatient_file_already_medication' => $element->inpatient_file_already_medication,
            'inpatient_file_patient_smoker' => $element->inpatient_file_patient_smoker,
            'inpatient_file_patient_smoking_frequency' => $element->inpatient_file_patient_smoking_frequency,
            'inpatient_file_is_diabetic' => $element->inpatient_file_is_diabetic,
            'inpatient_file_tuberculosis' => $element->inpatient_file_tuberculosis,
            'inpatient_file_hepatitis' => $element->inpatient_file_hepatitis,
            'inpatient_file_epilepsy' => $element->inpatient_file_epilepsy,
            'inpatient_file_rheumatic' => $element->inpatient_file_rheumatic,
            'inpatient_file_hiv' => $element->inpatient_file_hiv,
            'inpatient_file_is_heart_patient' => $element->inpatient_file_is_heart_patient,
            'inpatient_file_is_allergietic' => $element->inpatient_file_is_allergietic,
            'inpatient_file_prefer_anesthetic' => $element->inpatient_file_prefer_anesthetic,
            'inpatient_file_is_pregnant' => $element->inpatient_file_is_pregnant,
            'inpatient_file_patient_discomfirt_start' => $element->inpatient_file_patient_discomfirt_start,
            'inpatient_file_patient_is_first_visit' => $element->inpatient_file_patient_is_first_visit,
            'inpatient_file_patient_last_visit' => $element->inpatient_file_patient_last_visit,
            'inpatient_file_patient_last_visit_process' => $element->inpatient_file_patient_last_visit_process,
            'inpatient_file_patient_physician' => $element->inpatient_file_patient_physician,
            'inpatient_file_patient_physician_phone' => $element->inpatient_file_patient_physician_phone,
            'inpatient_file_patient_last_examination' => $element->inpatient_file_patient_last_examination,
            'inpatient_file_patient_under_medical' => $element->inpatient_file_patient_under_medical,
            'inpatient_file_service_id' => $element->inpatient_file_service_id,
            'inpatient_file_service_name' => $element->inpatient_file_service_name,
            'inpatient_file_room_id' => $element->inpatient_file_room_id,
            'inpatient_file_room_name' => $element->inpatient_file_room_name,
            'inpatient_file_panel_name' => $element->inpatient_file_panel_name,
            'inpatient_file_file_orignal_charges' => $element->inpatient_file_file_orignal_charges,
            'inpatient_file_file_charges' => $element->inpatient_file_file_charges,
            'inpatient_file_declared_loss' => $element->inpatient_file_declared_loss,
            'inpatient_file_declared_loss_by' => $element->inpatient_file_declared_loss_by,
            'inpatient_file_file_charges_paid' => $element->inpatient_file_file_charges_paid,
            'inpatient_file_open_on' => $element->inpatient_file_open_on,
            'inpatient_file_closed_on' => $element->inpatient_file_closed_on,
            'inpatient_file_will_occure_on' => $element->inpatient_file_will_occure_on,
            'inpatient_file_is_visiting' => $element->inpatient_file_is_visiting,
            'inpatient_file_modified_on' => $element->inpatient_file_modified_on,
            'inpatient_file_created_on' => $element->inpatient_file_created_on,
        ];

    }

    /**
     * Map service types
     */
    protected function mapServiceType($type)
    {
        return match($type) {
            'OPD' => TransactionElementType::OPD,
            'INPT' => TransactionElementType::IND,
            'EMER' => TransactionElementType::EMG,
            'DENTAL' => TransactionElementType::DNT,
            'ULTRA' => TransactionElementType::ULT,
            'EXP' => TransactionElementType::PETTY_CASH,
            'VOUCHER-PAY' => TransactionElementType::VOUCHER_PAY,
            'INPT-EXP' => TransactionElementType::IND_EXP,
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

    protected function getCachedExpenseCategory($id)
    {

        // Cache if not already cached
        if (!isset($this->expenseCategoryCache[$id])) {
            $category = ExpenseCategory::where('old_id', $id)->first();
            if ($category) {
                $this->expenseCategoryCache[$id] = $category;
            }
        }

        return $this->expenseCategoryCache[$id] ?? null;
    }

    protected function getCachedService($id, $type)
    {
        // Enum for service type to ensure consistent keys
        $type = $type instanceof TransactionElementType ? $type->name : $type;
        
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

        // For expenses, ensure positive values
        if ($isExpense && $numericValue < 0) {
            $this->warn("Expense transaction has negative amount {$numericValue}, converting to positive");
            return abs($numericValue);
        }

        return $numericValue;
    }

    protected function mapInpatientFileToServiceOrder($departmentTransactionId)
    {
        if (!$departmentTransactionId) {
            return null;
        }

        // Check cache first
        if (isset($this->serviceOrderCache[$departmentTransactionId])) {
            return $this->serviceOrderCache[$departmentTransactionId];
        }

        // Get the inpatient file based on department transaction ID
        $inpatientFile = DB::connection('secondary')
            ->table('inpatient_files')
            ->where('id', $departmentTransactionId)
            ->first();

        if ($inpatientFile) {
            // Cache the result
            $this->serviceOrderCache[$departmentTransactionId] = $inpatientFile;

            return [
                'old_id' => $inpatientFile->id,
                'patient_id' => $this->getCachedPatient($inpatientFile->patient_id)?->id,
                'admission_date' => $inpatientFile->admission_date,
                'discharge_date' => $inpatientFile->discharge_date,
                'created_at' => $inpatientFile->created_on,
                'updated_at' => $inpatientFile->modified_on,
            ];
        }

        return null;
    }


}