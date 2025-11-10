<?php

namespace App\Console\Commands;

use App\Models\Closing;
use App\Models\UpgradeProcess;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
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
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class fetchOld extends Command
{

    public static $TOTAL_STEPS = 70;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        Log::info('Starting fetch-old command execution.');

        ini_set('max_execution_time', 3600);
        set_time_limit(0);
        ini_set('memory_limit', -1);

        $statusObj = UpgradeProcess::firstOrCreate([
            'name' => 'currentStep'
        ] ,[
            'value' => 0
        ]);

        try {
            DB::connection('secondary')->getPdo();
        } catch (\Exception $e) {

            Log::error('Secondary database connection failed at step: ' . $statusObj->value);
            $this->error('Secondary database connection failed: ' . $e->getMessage());
            return;
        }

        $currentStep = $statusObj->value;

        switch ($currentStep){
            case 1:
                $this->images();
                break;
            case 2:
                $this->users();
                break;
            case 3:
                $this->services();
                break;
            case 4:
                $this->receptions();
                break;
            case 5:
                $this->patients(2020);
                break;
            case 6:
                $this->patients(2021, 1);
                break;
            case 7:
                $this->patients(2021, 2);
                break;
            case 8:
                $this->patients(2021, 3);
                break;
            case 9:
                $this->patients(2021, 4);
                break;
            case 10:
                $this->patients(2021, 5);
                break;
            case 11:
                $this->patients(2021, 6);
                break;
            case 12:
                $this->patients(2021, 7);
                break;
            case 13:
                $this->patients(2021, 8);
                break;
            case 14:
                $this->patients(2021, 9);
                break;
            case 15:
                $this->patients(2021, 10);
                break;
            case 16:
                $this->patients(2021, 11);
                break;
            case 17:
                $this->patients(2021, 12);
                break;
            case 18:
                $this->patients(2022, 1);
                break;
            case 19:
                $this->patients(2022, 2);
                break;
            case 20:
                $this->patients(2022, 3);
                break;
            case 21:
                $this->patients(2022, 4);
                break;
            case 22:
                $this->patients(2022, 5);
                break;
            case 23:
                $this->patients(2022, 6);
                break;
            case 24:
                $this->patients(2022, 7);
                break;
            case 25:
                $this->patients(2022, 8);
                break;
            case 26:
                $this->patients(2022, 9);
                break;
            case 27:
                $this->patients(2022, 10);
                break;
            case 28:
                $this->patients(2022, 11);
                break;
            case 29:
                $this->patients(2022, 12);
                break;
            case 30:
                $this->patients(2023, 1);
                break;
            case 31:
                $this->patients(2023, 2);
                break;
            case 32:
                $this->patients(2023, 3);
                break;
            case 33:
                $this->patients(2023, 4);
                break;
            case 34:
                $this->patients(2023, 5);
                break;
            case 35:
                $this->patients(2023, 6);
                break;
            case 36:
                $this->patients(2023, 7);
                break;
            case 37:
                $this->patients(2023, 8);
                break;
            case 38:
                $this->patients(2023, 9);
                break;
            case 39:
                $this->patients(2023, 10);
                break;
            case 40:
                $this->patients(2023, 11);
                break;
            case 41:
                $this->patients(2023, 12);
                break;
            case 42:
                $this->patients(2024, 1);
                break;
            case 43:
                $this->patients(2024, 2);
                break;
            case 44:
                $this->patients(2024, 3);
                break;
            case 45:
                $this->patients(2024, 4);
                break;
            case 46:
                $this->patients(2024, 5);
                break;
            case 47:
                $this->patients(2024, 6);
                break;
            case 48:
                $this->patients(2024, 7);
                break;
            case 49:
                $this->patients(2024, 8);
                break;
            case 50:
                $this->patients(2024, 9);
                break;
            case 51:
                $this->patients(2024, 10);
                break;
            case 52:
                $this->patients(2024, 11);
                break;
            case 53:
                $this->patients(2024, 12);
                break;
            case 54:
                $this->patients(2025, 1);
                break;
            case 55:
                $this->patients(2025, 2);
                break;
            case 56:
                $this->patients(2025, 3);
                break;
            case 57:
                $this->patients(2025, 4);
                break;
            case 58:
                $this->patients(2025, 5);
                break;
            case 59:
                $this->patients(2025, 6);
                break;
            case 60:
                $this->patients(2025, 7);
                break;
            case 61:
                $this->patients(2025, 8);
                break;
            case 62:
                $this->patients(2025, 9);
                break;
            case 63:
                $this->patients(2025, 10);
                break;
            case 64:
                $this->patients(2025, 11);
                break;
            case 65:
                $this->patients(2025, 12);
                break;
            case 66:
                $this->counterClosings();
                break;
            case 67:
                $this->expenseCategories();
                break;
            case 68:
                $this->vouchers();
                break;
            case 69:
                $this->expenses();
                break;
            case 70:
                $this->counterClosingTransactions();
                break;
            default:
                break;
        }

        if($currentStep >= self::$TOTAL_STEPS){
            return 0;
        }
        $statusObj->value = $currentStep + 1;
        $statusObj->save();
        
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

        $percObj = UpgradeProcess::firstOrCreate([
            'name' => 'percentage_synced'
        ] ,[
            'value' => $percentAgeCompleted
        ]);

        $percObj->value = $percentAgeCompleted;
        $percObj->save();


        $lastProcessedId = $statusObj->value;
        $batchSize = 1000;
        
        $transactions = DB::connection('secondary')
            ->table('reception_counters_closings_transactions')
            ->where('id', '>', $lastProcessedId)
            ->orderBy('id')
            ->limit($batchSize)
            ->get();
        
        if ($transactions->isEmpty()) {
            $this->inpatientDepartmentalServiceOrders();
            return;
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

                'created_at' => $transaction->created_on,
                'updated_at' => $transaction->modified_on,
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

                    'created_at' => $element->created_on,
                    'updated_at' => $element->modified_on,

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
                    
                    $newElement['income_or_expense'] = 'INCOME';
                    $newElement['patient_id'] = $trObject->patient_id ? $this->getPatient($trObject->patient_id)->id ?? null : null;

                    $s = DB::connection('secondary')->table('recestation_services')->where('id', $element->service_id)->first();

                    $serviceObj = ServiceRecestation::where('name', $s->name)->first();
                    
                    $newElement['service_recestation_id'] = $serviceObj ? $serviceObj->id : null;
                    $newElement['type'] = 'RECES'. ($serviceObj ? '-'.$serviceObj->department->slug : null);

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


    protected function inpatientDepartmentalServiceOrders(){


        $statusObj = UpgradeProcess::firstOrCreate([
            'name' => 'inpatient_file_id'
        ] ,[
            'value' => 0
        ]);
        
        if($statusObj->value == 'finished'){
            return;
        }

        $availableRecords = DB::connection('secondary')->table('inpatient_file')->count();

        $transferedRecords = ServiceOrder::where('type','')->count();

        $percentAgeCompleted = ($transferedRecords / $availableRecords) * 100;

        $percObj = UpgradeProcess::firstOrCreate([
            'name' => 'inpatient_percentage_synced'
        ] ,[
            'value' => $percentAgeCompleted
        ]);

        $percObj->value = $percentAgeCompleted;
        $percObj->save();


        $lastProcessedId = $statusObj->value;
        $batchSize = 100;
        
        $files = DB::connection('secondary')
            ->table('inpatient_file')
            ->where('id', '>', $lastProcessedId)
            ->orderBy('id')
            ->limit($batchSize)
            ->get();
        foreach ($files as $file) {

            $dpartmentTransaction = DB::connection('secondary')
                ->table('inpatient_transactions')
                ->where('file_id', $file->id)
                ->first();

            $ourTransactionRecord = Transaction::where('old_id', $dpartmentTransaction->reception_transaction_id)->first();

            foreach($ourTransactionRecord->elements as $element){

                $serviceOrder = ServiceOrder::where('id', $element->service_order_id)->first();

                if($serviceOrder){
                    
                    $soNumber = $serviceOrder->so_number;

                    $exploded = explode('/', $soNumber);

                    $soNumber[6] = str_pad($file->id, 8, '0', STR_PAD_LEFT);
                    $serviceOrder->so_number = implode('/', $exploded);
                    $serviceOrder->so_short = implode('/', [
                        $exploded[5],
                        $exploded[6],
                    ]);
                    $serviceOrder->save();
                }
            }

        }
    }

    protected function expenses(){
        DB::connection('secondary')->table('expenses')->orderBy('id')->chunk(100, function ($expenses) {
            foreach ($expenses as $expense) {

                $new = [
                    // 'old_id' => 'EXP-'.$expense->id,
                    'voucher_id' => $expense->voucher_id,
                    'exp_category_id' => $expense->category_id && $expense->category_id != 0 ? ExpenseCategory::where('name', $expense->category_id)->first()->id ?? null : null,
                    'type' => $expense->payment_type,
                    // 'notes' => $expense->payment_reference,
                    // 'notes_json' => [],
                    // 'service_order_id' => $expense->service_order_id,
                    'payed_to' => $expense->payed_to && $expense->payed_to != 0 ? User::where('id', $expense->payed_to)->first()->id ?? null : null,
                    'payed_to_name' => $expense->payment_reference,
                    'amount' => $expense->amount_received_num,
                    'amount_alphabetical' => $expense->amount_received_words,
                    'created_at' => $expense->created_on,
                    'updated_at' => $expense->modified_on,
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
                    'amount' => $expense->amount_in_num,
                    'amount_alphabetical' => $expense->amount_in_figure,

                    'created_at' => $expense->created_on,
                    'updated_at' => $expense->modified_on,
                ];

                Expense::updateOrCreate([
                    'old_id' => 'INP-EXP-'.$expense->id
                ], $new);
            }
        });
    }

    protected function vouchers(){
        DB::connection('secondary')->table('expense_vouchers')->orderBy('id')->chunk(100, function ($vouchers) {
            foreach ($vouchers as $voucher) {

                $new = [
                    'exp_category_id' => $voucher->exp_category_id,
                    'service_order_id' => null,
                    'payed_to' => $voucher->employee_id ? User::where('id', $voucher->employee_id)->first()->id ?? null : null,
                    'payed_to_name' => $voucher->payed_to_others,
                    'amount' => $voucher->exp_amount_numbers ? ($voucher->exp_amount_numbers < 0 ? ($voucher->exp_amount_numbers * -1) : $voucher->exp_amount_numbers) : 0,
                    'created_at' => $voucher->created_on,
                    'updated_at' => $voucher->modified_on,
                ];

                ExpenseVoucher::updateOrCreate([
                    'old_id' => $voucher->id
                ], $new);
            }
        });
    }

    protected function expenseCategories(){
        DB::connection('secondary')->table('expenses_categories')->orderBy('id')->chunk(100, function ($categories) {
            foreach ($categories as $category) {

                $new = [
                    'name' => $category->name,
                ];

                ExpenseCategory::updateOrCreate([
                    'old_id' => $category->id
                ], $new);
            }
        });
    }

    protected function counterClosings(){
        DB::connection('secondary')->table('reception_counters_closings')->orderBy('id')->chunk(100, function ($closings) {
            foreach ($closings as $closing) {

                // Check how many closings were created that month
                $countInMonth = Closing::whereYear('created_at', Carbon::parse($closing->created_on)->year)
                    ->whereMonth('created_at', Carbon::parse($closing->created_on)->month)
                    ->count();

                $ctNumber = 'CT/' . Carbon::parse($closing->created_on)->format('Y/m/') . str_pad($countInMonth + 1, 4, '0', STR_PAD_LEFT);

                $new = [
                    'reception_id' => Reception::where('id', $closing->reception_id)->first()->id ?? null,
                    'receptionist_id' => User::where('id', $closing->user_id)->first()->id ?? null,
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
                Closing::updateOrCreate([
                    'old_id' => $closing->id
                ], $new);
            }
        });
    }

    public function patients($year, $month = false){

        $query = DB::connection('secondary')->table('patients');

        if($month){
            $query->whereMonth('created_on', $month);
        }

        $query->whereYear('created_on', $year);

        $query->orderBy('id')->chunk(1000, function ($patients) {


            foreach ($patients as $patient) {

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

                Patient::updateOrCreate([
                    'id' => $patient->id
                ], $newPatient);
            }
        });
    }

    public function receptions(){
        DB::connection('secondary')->table('reception_counters')->orderBy('id')->chunk(1000, function ($receptions) {
            foreach ($receptions as $reception) {

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

                Reception::updateOrCreate([
                    'id' => $reception->id
                ], $newReception);
            }
        });
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
    
    protected function users(){
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

    protected function getExpenseObject($id){

        if(!$id || $id == 0){
            return null;
        }
        $expenseObj = Expense::where('old_id', $id)->first();

        if($expenseObj){
            return $expenseObj;
        }

        if(str_starts_with($id, 'EXP-')){
            $expense = DB::connection('secondary')->table('expenses')->find((str_replace('EXP-' , '' , $id)));
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
        else if(str_starts_with($id, 'INP-EXP-')){
            $expense = DB::connection('secondary')->table('inpatient_expense_transactions')->find((str_replace('INP-EXP-' , '' , $id)));
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

}
