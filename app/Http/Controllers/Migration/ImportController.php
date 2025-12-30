<?php

namespace App\Http\Controllers\Migration;

use App\Http\Controllers\Controller;
use App\Models\Closing;
use App\Models\Dentist;
use App\Models\EmergencyDoctor;
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
use App\Models\ServiceRecestation;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\UpgradeProcess;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ImportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        // Prevent time out give execution as much time as it needs and memory
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $currentStep = $request->get('step' , 0);
        
        $this->images();
        $this->users();
        $this->services();

        switch ($currentStep){
            case 1:
                return $this->counterClosingTransactions();
                break;
            case 5:
                return $this->expenses();
                break;
            
            default:
                return Inertia::render('migration/import',[
                    'steps' => [
                        4 => 'Counter Closing Transactions',
                        5 => 'Expenses',
                    ]
                ]);
                break;
        }
    }

    protected function expenses(){
        DB::connection('secondary')->table('expenses')->orderBy('id')->chunk(100, function ($expenses) {
            foreach ($expenses as $expense) {

                $new = [
                    'old_id' => $expense->id,
                    'voucher_id' => $expense->voucher_id,
                    'exp_category_id' => $expense->category_id && $expense->category_id != 0 ? ExpenseCategory::where('name', $expense->category_id)->first()->id ?? null : null,
                    'type' => $expense->payment_type,
                    // 'notes' => $expense->payment_reference,
                    // 'notes_json' => [],
                    // 'service_order_id' => $expense->service_order_id,
                    'payed_to' => $expense->payed_to && $expense->payed_to != 0 ? User::where('id', $expense->payed_to)->first()->id ?? null : null,
                    'payed_to_name' => $expense->payment_reference,
                    'amount' => $expense->amount_received_num,
                    'amount_alphabetical' => $expense->amount_received_words
                ];

                Expense::updateOrCreate([
                    'old_id' => 'EXP-'.$expense->id
                ], $new);
            }
        });

        DB::connection('secondary')->table('inpatient_expense_transactions')->orderBy('id')->chunk(100, function ($expenses) {
            foreach ($expenses as $expense) {

                $new = [
                    
                    'type' => $expense->payment_type,
                    // 'notes' => $expense->payment_reference,
                    // 'notes_json' => [],
                    // 'service_order_id' => $expense->service_order_id,
                    'payed_to' => $expense->receaved_by && $expense->receaved_by != 0 ? User::where('id', $expense->receaved_by)->first()->id ?? null : null,
                    'payed_to_name' => $expense->payment_refference,
                    'amount' => $expense->amount_received_num,
                    'amount_alphabetical' => $expense->amount_received_words
                ];

                Expense::updateOrCreate([
                    'old_id' => 'INP-EXP-'.$expense->id
                ], $new);
            }
        });
    }

    protected function counterClosingTransactions(){


        $statusObj = UpgradeProcess::firstOrCreate([
            'name' => 'transaction_id'
        ] ,[
            'value' => 0
        ]);
        
        if($statusObj->value == 'finished'){
            return;
        }

        $availableRecords = DB::connection('secondary')->table('reception_counters_closings_transactions')->count();

        $transferedRecords = Transaction::count();

        $percentAgeCompleted = ($transferedRecords / $availableRecords) * 100;

        UpgradeProcess::firstOrCreate([
            'name' => 'percentage_synced'
        ] ,[
            'value' => $percentAgeCompleted
        ]);


        $lastProcessedId = $statusObj->value;
        $batchSize = 100;
        
        $transactions = DB::connection('secondary')
            ->table('reception_counters_closings_transactions')
            ->where('id', '>', $lastProcessedId)
            ->orderBy('id')
            ->limit($batchSize)
            ->get();
        
        if ($transactions->isEmpty()) {
            // Reset the session when done
            $statusObj->value = 'finished';
            $statusObj->save();
        }
        
        foreach ($transactions as $transaction) {

            $new = [
                'closing_id' => $this->getCounter($transaction->counter_id)->id ?? null,
                'created_by' => $this->getUser($transaction->user_id)->id ?? null,
                'patient_id' => $transaction->patient_id ? $this->getPatient($transaction->patient_id)->id ?? null : null,
                'type' => $transaction->type == 'CASH' ? 'CASH' : ($transaction->type == 'CARD' || $transaction->type == 'CREDITCARD' ? 'CARD' : ($transaction->type == 'CHEQUE' ? 'CHEQUE' : 'CASH')),
                'income_or_expense' => $transaction->income_or_expence == 'INCOME' ? 'INCOME' : 'EXPENSE',
                'amount' => $transaction->amount,
                'orignal_amount' => $transaction->orignal_amount,
                'customer_payed' => $transaction->customer_payed,
                'change' => $transaction->change,
                'edited_amount' => $transaction->edited_amount,
            ];

            $trObject = Transaction::updateOrCreate([
                'old_id' => $transaction->id
            ], $new);

            $elements = DB::connection('secondary')->table('reception_counters_closings_transaction_elements')->where('id', $transaction->id)->get();

            foreach ($elements as $element) {

                $newElement = [
                    'closing_id' => $trObject->closing_id,
                    'transaction_id' => $trObject->id,
                    'created_by' => $trObject->created_by,

                ];

                if($element->type == 'OPD'){
                    
                    
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['doctor_id'] = $element->doctor_id ? $this->getUser($element->doctor_id)->id ?? null : null;
                    $newElement['patient_id'] = $element->patient_id ? $this->getPatient($element->patient_id)->id ?? null : null;

                    $serviceObj = $this->getService($element->service_id, 'OPD');
                    
                    $newElement['service_id'] = $serviceObj ? $serviceObj->id : null;

                    $newElement['type'] = $serviceObj ? $serviceObj->department->slug : null;

                }else if($element->type == 'INPT'){
                    
                    
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['doctor_id'] = $element->doctor_id ? $this->getUser($element->doctor_id)->id ?? null : null;
                    $newElement['patient_id'] = $element->patient_id ? $this->getPatient($element->patient_id)->id ?? null : null;

                    $serviceObj = $this->getService($element->service_id, 'IND');
                    
                    $newElement['service_id'] = $serviceObj ? $serviceObj->id : null;

                    $newElement['type'] = $serviceObj ? $serviceObj->department->slug : null;

                }else if($element->type == 'EMER'){
                    
                    
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['patient_id'] = $element->patient_id ? $this->getPatient($element->patient_id)->id ?? null : null;

                    $serviceObj = $this->getService($element->service_id, 'EMG');
                    
                    $newElement['service_id'] = $serviceObj ? $serviceObj->id : null;

                    $newElement['type'] = $serviceObj ? $serviceObj->department->slug : null;

                }else if($element->type == 'DENTAL'){
                    
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['doctor_id'] = $element->doctor_id ? $this->getUser($element->doctor_id)->id ?? null : null;
                    $newElement['patient_id'] = $trObject->patient_id ? $this->getPatient($trObject->patient_id)->id ?? null : null;

                    $serviceObj = $this->getService($element->service_id, 'DNT');
                    
                    $newElement['service_id'] = $serviceObj ? $serviceObj->id : null;

                    $newElement['type'] = $serviceObj ? $serviceObj->department->slug : null;

                }else if($element->type == 'ULTRA'){
                    
                    
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['doctor_id'] = $element->doctor_id ? $this->getUser($element->doctor_id)->id ?? null : null;
                    $newElement['patient_id'] = $trObject->patient_id ? $this->getPatient($trObject->patient_id)->id ?? null : null;

                    $serviceObj = $this->getService($element->service_id, 'ULT');
                    
                    $newElement['service_id'] = $serviceObj ? $serviceObj->id : null;

                    $newElement['type'] = $serviceObj ? $serviceObj->department->slug : null;
                    
                }else if($element->type == 'RECES'){
                    
                    $newElement['type'] = 'RECES';
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['patient_id'] = $trObject->patient_id ? $this->getPatient($trObject->patient_id)->id ?? null : null;

                    $s = DB::connection('secondary')->table('recestation_services')->where('id', $element->service_id)->first();

                    $serviceObj = ServiceRecestation::where('name', $s->name)->first();
                    
                    $newElement['service_recestation_id'] = $serviceObj ? $serviceObj->id : null;
                }else if($element->type == 'EXP'){
                    
                    $newElement['type'] = 'EXP';
                    $newElement['income_or_expense'] = 'EXPENSE';

                    $expenseObj = $this->getExpenseObject('EXP-'.$element->department_transaction_id);

                    $newElement['expense_id'] = $expenseObj ? $expenseObj->id : null;


                }else if($element->type == 'VOUCHER-PAY'){
                    
                    $newElement['type'] = 'VOUCHER-PAY';
                    $newElement['income_or_expense'] = 'EXPENSE';

                    $expenseObj = $this->getExpenseObject('EXP-'.$element->department_transaction_id);

                    $newElement['expense_id'] = $expenseObj ? $expenseObj->id : null;

                    $voucherObj = ExpenseVoucher::where('id', $expenseObj->voucher_id)->first();

                    $newElement['exp_voucher_id'] = $voucherObj ? $voucherObj->id : null;

                }else if($element->type == 'INPT-EXP'){
                    
                    $newElement['type'] = 'INPT-EXP';
                    $newElement['income_or_expense'] = 'EXPENSE';
                    $newElement['patient_id'] = $trObject->patient_id ? Patient::where('id', $trObject->patient_id)->first()->id ?? null : null;

                    $expenseObj = $this->getExpenseObject('INP-EXP-'.$element->department_transaction_id);

                    $newElement['expense_id'] = $expenseObj ? $expenseObj->id : null;

                    $voucherObj = ExpenseVoucher::where('id', $expenseObj->voucher_id)->first();

                    $newElement['exp_voucher_id'] = $voucherObj ? $voucherObj->id : null;
                }else{

                    dd('Unknown Type '.$element->type);
                }
                $transObjModel = TransactionElement::updateOrCreate([
                    'old_id' => $element->id
                ], $newElement);
            }
            $statusObj->value = $transaction->id;
            $statusObj->save();
        }
    }



    protected function getExpenseObject($id){

        if(!$id || $id == 0){
            return null;
        }
        $expenseObj = Expense::where('old_id', $id)->first();

        if($expenseObj){
            return $expenseObj;
        }

        if(str_starts_with($id, 'INP-EXP-')){
            $expense = DB::connection('secondary')->table('expenses')->find((str_replace('INP-EXP-' , '' , $id)));
            $new = [
                'old_id' => $expense->id,
                'voucher_id' => $expense->voucher_id,
                'exp_category_id' => $expense->category_id && $expense->category_id != 0 ? ExpenseCategory::where('name', $expense->category_id)->first()->id ?? null : null,
                'type' => $expense->payment_type,
                // 'notes' => $expense->payment_reference,
                // 'notes_json' => [],
                // 'service_order_id' => $expense->service_order_id,
                'payed_to' => $expense->payed_to && $expense->payed_to != 0 ? User::where('id', $expense->payed_to)->first()->id ?? null : null,
                'payed_to_name' => $expense->payment_reference,
                'amount' => $expense->amount_received_num,
                'amount_alphabetical' => $expense->amount_received_words
            ];

            $expenseObj = Expense::updateOrCreate([
                'old_id' => 'EXP-'.$expense->id
            ], $new);
        }
        else if(str_starts_with($id, 'EXP-')){
            $expense = DB::connection('secondary')->table('inpatient_expense_transactions')->find((str_replace('EXP-' , '' , $id)));
            $new = [
                'type' => $expense->payment_type,
                // 'notes' => $expense->payment_reference,
                // 'notes_json' => [],
                // 'service_order_id' => $expense->service_order_id,
                'payed_to' => $expense->receaved_by && $expense->receaved_by != 0 ? User::where('id', $expense->receaved_by)->first()->id ?? null : null,
                'payed_to_name' => $expense->payment_refference,
                'amount' => $expense->amount_in_num,
                'amount_alphabetical' => $expense->amount_in_figure
            ];

            $expenseObj = Expense::updateOrCreate([
                'old_id' => 'INP-EXP-'.$expense->id
            ], $new);
        }

        return $expenseObj;

    }

    protected function getCounter($int){

        if(!$int || $int == 0){
            return null;
        }

        $closingObj = Closing::where('id', $int)->first();

        if($closingObj){
            return $closingObj;
        }

        $closing = DB::connection('secondary')->table('reception_counters_closings')->find($int);

        // Check how many closings were created that month
        $countInMonth = Closing::whereYear('created_at', Carbon::parse($closing->created_on)->year)
            ->whereMonth('created_at', Carbon::parse($closing->created_on)->month)
            ->count();

        $ctNumber = 'CT/' . Carbon::parse($closing->created_on)->format('Y/m/') . str_pad($countInMonth + 1, 4, '0', STR_PAD_LEFT);

        $new = [
            'reception_id' => $this->getReception($closing->reception_id)->id ?? null,
            'receptionist_id' => $this->getUser($closing->user_id)->id ?? null,
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
        ];
        $closingObj = Closing::updateOrCreate([
            'old_id' => $closing->id
        ], $new);

        return $closingObj;

    }

    protected function getReception($int){

        if(!$int || $int == 0){
            return null;
        }

        $receptionObj = Reception::where('id', $int)->first();

        if($receptionObj){
            return $receptionObj;
        }

        $reception = DB::connection('secondary')->table('reception_counters')->find($int);

        $allowedDepartments = [
            $reception->is_opd_allowed => 'OPD',
            $reception->is_inpatient_allowed => 'IND',
            $reception->is_emergency_allowed => 'EMG',
            'DNT',
            'PTH',
            'ULT',
            'XRY'
        ];
        
        $newReception = [
            'name' => $reception->counter_name,
            'allowed_departments' => $allowedDepartments,
            'is_allowed_to_pay_voucher' => $reception->is_allowed_to_pay_voucher,
            'is_allowed_to_pay_from_petty_cash' => $reception->is_allowed_to_pay_from_petty_cash,
            'is_cash_allowed' => $reception->cash_on_counter,
            'is_cheques_allowed' => $reception->cheques_on_counter,
            'is_card_allowed' => $reception->card_slips_on_counter,
        ];

        $receptionObj = Reception::updateOrCreate([
            'id' => $reception->id
        ], $newReception);

        return $receptionObj;
    }

    protected function getUser($int){

        if(!$int || $int == 0){
            return null;
        }
        $userObj = User::where('id', $int)->first();

        if($userObj){
            return $userObj;
        }

        $user = DB::connection('secondary')->table('aauth_users')->find($int);

        $newUser = [
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
            'profile_img_path' => $user->profile_img_path,
            'profile_img_id' => $user->profile_img_id,
            'created_at' => $user->created_on,
            'updated_at' => $user->modified_on,
        ];

        $userObj = User::updateOrCreate(['email' => $user->email], $newUser);

        $profiles = [
            $user->is_super_admin => $userObj->adminProfiles(),
            $user->is_receptionist => $userObj->receptionistProfiles(),
            $user->is_opd_doctor => $userObj->opdDoctorProfiles(),
            $user->is_inpatient_doctor => $userObj->indDoctorProfiles(),
            $user->is_emergency_doctor => $userObj->emergencyDoctorProfiles(),
            $user->is_dentist => $userObj->dentistProfiles(),
            $user->is_ultrasound_doc => $userObj->ultrasoundDoctorProfiles(),
            $user->is_xray_tech => $userObj->xrayTechnicianProfiles()
        ];

        foreach($profiles as $condition => $profile) {
            if ($condition) {
                $profile->updateOrCreate([
                    'user_id' => $userObj->id,
                ], [
                    'created_at' => $user->created_on,
                    'updated_at' => $user->modified_on,
                ]);
            }
        }

    }

    protected function getPatient($int){

        if(!$int || $int == 0){
            return null;
        }

        $patientObj = Patient::where('id', $int)->first();

        if($patientObj){
            return $patientObj;
        }

        $patient = DB::connection('secondary')->table('patients')->find($int);

        $createdInTheMonth = Carbon::parse($patient->created_on);

        // Count how many patients were created in that month
        $counter = Patient::whereYear('created_at', $createdInTheMonth->format('Y'))
            ->whereMonth('created_at', $createdInTheMonth->format('m'))
            ->count();

        $counter ++;

        $psNumber = 'PS/' . $createdInTheMonth->format('Y/m') .'/'. str_pad($counter, 6, '0', STR_PAD_LEFT);;

        $newPatient = [
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

        $patientObj = Patient::updateOrCreate([
            'id' => $patient->id
        ], $newPatient);

        return $patientObj;
    }

    protected function getService($int, $type){

        if(!$int || $int == 0){
            return null;
        }

        if($type == 'OPD'){
            $table = 'opd_services';
        }elseif($type == 'IND'){
            $table = 'inpd_services';
        }elseif($type == 'EMG'){
            $table = 'emergency_services';
        }elseif($type == 'DNT'){
            $table = 'dental_services';
        }elseif($type == 'PTH'){
            $table = 'test_services';
        }elseif($type == 'ULT'){
            $table = 'ultrasound_services';
        }elseif($type == 'XRY'){
            $table = 'xray_services';
        }else{
            dd('Unknown Service Type '.$type);
        }

        $s = DB::connection('secondary')->table($table)->where('id', $int)->first();

        if(!$s){
            dd('Service Not Found ID '.$int.' in '.$table.' for type '.$type);
        }

        $department = ServiceDepartment::where('slug', $type)->first();

        if(!$department){
            ServiceDepartment::updateOrCreate([
                'slug' => $type
            ],[
                'name' => $type,
                'image' => "/img/".Str::lower($type).".png",
            ]);
        }

        $serviceObj = Service::with('department')->where('name', $s->name)->where('service_department_id', $department->id)->first();

        if(!$serviceObj){


            $serviceObj = Service::updateOrCreate([
                'name' => $s->name,
                'service_department_id' => $department->id,
            ],[
                'slug' => $s->post_key,
                'charges' => $s->charges,
                'charges_include_tax' => $s->charges_including_tax,
                'tax_rate' => $s->tax_rate,
                'have_service_provider' => in_array(
                    $department->key, ['OPD', 'IND']
                ) &&  $s->is_doctor_selectable,
                'is_composit_service' => $department->have_composit_services,
                'created_by' => $s->entered_by
            ]);

        }

        return $serviceObj;
    }

    public function services(){

        foreach([
            [
                'key' => 'OPD',
                'name' => "Outdoor",
                'image' => "/img/opd.png",
                'table' => 'opd_services',
            ],

            [
                'key' => 'IND',
                'name' => "Indoor",
                'image' => "/img/ind.png",
                'table' => 'inpd_services',
                'recesitation_table' => 'recestation_services'
            ],

            [
                'key' => 'EMG',
                'name' => "Emergency",
                'image' => "/img/emergency.png",
                'table' => 'emergency_services',
            ],

            [
                'key' => 'DNT',
                'name' => "Dental Department",
                'image' => "/img/dental.png",
                'table' => 'dental_services',
            ],

            [
                'key' => 'PTH',
                'name' => "Laboratory",
                'image' => "/img/laboratory.png",
                'table' => 'test_services',
            ],

            [
                'key' => 'ULT',
                'name' => "Ultrasound",
                'image' => "/img/ultrasound.png",
                'table' => 'ultrasound_services',
            ],

            [
                'key' => 'XRY',
                'name' => "Radiology",
                'image' => "/img/xray.png",
                'table' => 'xray_services',
            ]
        ] as $row){
            $department = ServiceDepartment::updateOrCreate([
                'slug' => $row['key']
            ],[
                'name' => $row['name'],
                'image' => $row['image'],
                'have_composit_services' => $row['key'] === 'IND'
            ]);

            DB::connection('secondary')->table($row['table'])->orderBy('id')->chunk(100, function ($services) use ($department) {

                foreach($services as $service){
                    $newService = [
                        'name' => $service->name,
                        'slug' => $service->post_key,
                        'service_department_id' => $department->id,
                        'charges' => $service->charges,
                        'charges_include_tax' => $service->charges_including_tax,
                        'tax_rate' => $service->tax_rate,
                        'have_service_provider' => in_array(
                            $department->key, ['OPD', 'IND']
                        ) &&  $service->is_doctor_selectable,
                        'is_composit_service' => $department->have_composit_services,
                        'created_by' => $service->entered_by
                    ];

                    if($service->is_doctor_selectable && $department->key == 'OPD'){
                        $newService['service_provider_types'] = [
                            OpdDoctor::class
                        ];
                    }

                    if($service->is_doctor_selectable && $department->key == 'IND'){
                        $newService['service_provider_types'] = [
                            IndDoctor::class
                        ];
                    }

                    if($service->is_doctor_selectable && $department->key == 'DNT'){
                        $newService['service_provider_types'] = [
                            Dentist::class
                        ];
                    }

                    Service::updateOrCreate([
                        'slug' => $service->post_key,
                        'service_department_id' => $department->id,
                    ], $newService);

                }

                
            });

            if(array_key_exists('recesitation_table', $row)){

                DB::connection('secondary')->table($row['recesitation_table'])->orderBy('id')->chunk(100, function ($services) use ($department) {

                    foreach($services as $service){


                        $newService = [
                            'name' => $service->name,
                            'slug' => $service->post_key == 0 ? null : $service->post_key,
                            'service_department_id' => $department->id,
                            'charges' => $service->charges,
                            'charges_include_tax' => $service->charges_including_tax,
                            'tax_rate' => $service->tax_rate,
                            'created_by' => $service->entered_by
                        ];

                        ServiceRecestation::updateOrCreate([
                            'slug' => $service->post_key,
                            'service_department_id' => $department->id,
                        ], $newService);

                    }

                });
            }
        }

    }
    
    protected function users()
    {
        DB::connection('secondary')->table('aauth_users')->orderBy('id')->chunk(100, function ($users) {
            foreach ($users as $user) {
                $newUser = [
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
                    'profile_img_path' => $user->profile_img_path,
                    'profile_img_id' => $user->profile_img_id,
                    'created_at' => $user->created_on,
                    'updated_at' => $user->modified_on,
                ];

                $userModel = User::updateOrCreate(['email' => $user->email], $newUser);

                $profiles = [
                    $user->is_super_admin => $userModel->adminProfiles(),
                    $user->is_receptionist => $userModel->receptionistProfiles(),
                    $user->is_opd_doctor => $userModel->opdDoctorProfiles(),
                    $user->is_inpatient_doctor => $userModel->indDoctorProfiles(),
                    $user->is_emergency_doctor => $userModel->emergencyDoctorProfiles(),
                    $user->is_dentist => $userModel->dentistProfiles(),
                    $user->is_ultrasound_doc => $userModel->ultrasoundDoctorProfiles(),
                    $user->is_xray_tech => $userModel->xrayTechnicianProfiles()
                ];

                foreach($profiles as $condition => $profile) {
                    if ($condition) {
                        $profile->updateOrCreate([
                            'user_id' => $userModel->id,
                        ], [
                            'created_at' => $user->created_on,
                            'updated_at' => $user->modified_on,
                        ]);
                    }
                }
            }
        });
    }

    protected function images(){
        DB::connection('secondary')->table('images')->orderBy('id')->chunk(100, function ($images) {
            foreach ($images as $image) {
                $newImage = [
                    'path' => $image['path'],
                    'owner_id' => $image['owner_id']
                ];
                Image::updateOrCreate($newImage);
            }
        });
    }
}