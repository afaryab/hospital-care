<?php

namespace App\Http\Controllers;

use App\Enum\CounterStatus;
use App\Enum\TransactionElementType;
use App\Models\Closing;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\ServiceRecestation;
use App\Models\Transaction;
use App\Models\TransactionElement;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $profiles = collect()->merge([
            'admin' => $user->adminProfiles()->get(),
            $user->accountantProfiles()->get(),
            $user->receptionistProfiles()->get(),
            $user->opdDoctorProfiles()->get(),
            $user->indDoctorProfiles()->get(),
            $user->emergencyDoctorProfiles()->get(),
            $user->dentistProfiles()->get(),
            $user->ultrasoundDoctorProfiles()->get(),
            $user->xrayTechnicianProfiles()->get(),
            $user->nursingStaffProfiles()->get(),
        ]);

        return Inertia::render('dashboard');
    }


    public function register($year = false, $month = false){

        $query = Patient::query();

        $year && $query->whereYear('created_at', $year);
        $month && $query->whereMonth('created_at', $month);

        $data = $query->paginate(8);

        $serviceDepartments = ServiceDepartment::all();

        return Inertia::render('register',[
            'yearSelected' => $year,
            'monthSelected' => $month,
            'patientsPaginated' => $data,
            'serviceDepartments' => $serviceDepartments
        ]);


    }


    public function patient($year, $month, $number, $departmentKey = false, $serviceNumber = false){

        $psNumber = 'PS/'.$year.'/'.$month.'/'.$number;

        $patientData = Patient::with('treatments')->where('ps_number', $psNumber)->firstOrFail();

        $serviceDepartments = ServiceDepartment::all();
        $serviceOrder = null;

        if($serviceNumber){

            $soNumber = 'PS/'.$year.'/'.$month.'/'.$number.'/'.$departmentKey.'/'.$serviceNumber;
        
            $serviceOrder = ServiceOrder::where('so_number', $soNumber)->firstOrFail();

        }
        
        return Inertia::render('patient',[
            'departmentKey' => $departmentKey,
            'patientData' => $patientData,
            'serviceDepartments' => $serviceDepartments,
            'serviceOrder' => $serviceOrder,
        ]);

        
    }


    public function treatment($year, $month, $number, $departmentKey, $treatment){

        $psNumber = 'PS/'.$year.'/'.$month.'/'.$number;

        $patientData = Patient::where('ps_number', $psNumber)->firstOrFail();

        return Inertia::render('patient',[
            'departmentKey' => $departmentKey,
            'treatmentKey' => $treatment,
            'patientData' => $patientData,
        ]);

    }

    public function counter(Request $request)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', $request->user()->id)->first();

        if(!$openCounter){
            return redirect(route('counter-open'));
        }else{
            return redirect(route('counter-view',[
                'ctYear' => $openCounter->year,
                'ctMonth' => $openCounter->month,
                'ctNumber' => $openCounter->number
            ]));
        }
    }

    public function userCountersList($year = false, $month = false)
    {
        $query = Closing::query();

        $query->where('receptionist_id', request()->user()->id);

        $year && $query->whereYear('created_at', $year);
        $month && $query->whereMonth('created_at', $month);

        $data = $query->paginate(8);

        return Inertia::render('counter/list',[
            'yearSelected' => $year,
            'monthSelected' => $month,
            'closings' => $data,
        ]);
    }

    public function counterOpen(Request $request)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', $request->user()->id)->first();
        
        if($openCounter){
            return redirect(route('counter-view',[
                'ctYear' => $openCounter->year,
                'ctMonth' => $openCounter->month,
                'ctNumber' => $openCounter->number
            ]));
        }else{

            return Inertia::render('counter/open',[
                'recptions' => Reception::all()
            ]);
        }
    }

    public function counterStore(Request $request)
    {

        $request->validate([
            'opening_balance' => 'nullable|numeric',
            'reception_id' => 'required|exists:receptions,id'
        ]);

        $counter = Closing::create([
            'reception_id' => $request->reception_id,
            'receptionist_id' => $request->user()->id,
            'ct_number' => Closing::generateCounterNumber(),
            'status' => CounterStatus::OPEN,
            'opening_amount' => $request->opening_balance ?? 0
        ]);

        return redirect(route('counter-view',[
            'ctYear' => $counter->year,
            'ctMonth' => $counter->month,
            'ctNumber' => $counter->number
        ]));
    }

    public function counterClose(Request $request)
    {
        $openCounter = Closing::where('status','open')->where('receptionist_id', $request->user()->id)->first();
        
        if(!$openCounter){
            return redirect(route('counter-open'));
        }else{

            if($request->isMethod('post')){
                
                // Sum elements amounts
                $totalAmount = $openCounter->transactions()->sum('amount');
                $openCounter->closing_amount = $totalAmount;
                // Close the counter
                $openCounter->status = CounterStatus::CLOSED;
                $openCounter->closed_at = now();
                $openCounter->save();

                return redirect(route('counter-view',[
                    'ctYear' => $openCounter->year,
                    'ctMonth' => $openCounter->month,
                    'ctNumber' => $openCounter->number
                ]));
            }

            $totalIncAmount = $openCounter->transactions()->where('income_or_expense','INCOME')->sum('amount');
            $totalExpAmount = $openCounter->transactions()->where('income_or_expense','EXPENSE')->sum('amount');
            $openCounter->closing_amount = $totalIncAmount - $totalExpAmount;
            $openCounter->expense_payed = $totalExpAmount;
            $openCounter->save();


            return Inertia::render('counter/close',[
                'openCounter' => $openCounter
            ]);
        }
    
    }

    public function counterView($ctYear = '', $ctMonth = '', $ctNumber = '', Request $request)
    {

        $ctNumber = 'CT/'.$ctYear.'/'.$ctMonth.'/'.$ctNumber;

        $openCounter = Closing::with('transactions','transactions.patient', 'transactions.elements', 'transactions.patient', 'transactions.elements.service', 'transactions.elements.serviceOrder', 'transactions.elements.expense', 'transactions.elements.expVoucher')->where('ct_number',$ctNumber)->first();

        //->where('receptionist_id', $request->user()->id)

        // dd($openCounter);

        if(!$openCounter){
            return redirect(route('counter-open'));
        }else{
            // dd($openCounter);
            return Inertia::render('counter/view',[
                'openCounter' => $openCounter,
            ]);
        }
    }

    public function counterPatient($pYear = false, $pMonth = false, $number = false, $departmentKey = false)
    {
        $openCounter = Closing::with('transactions')->where('status','open')->where('receptionist_id', request()->user()->id)->first();

        if(!$openCounter){
            return redirect(route('counter-open'));
        }
        $pageData = [
            'openCounter' => $openCounter
        ];

        if($pYear || $pMonth || $number){

            $psNumber = 'PS/'.$pYear.'/'.$pMonth.'/'.$number;

            $patientData = Patient::with('treatments','transactions')->where('ps_number', $psNumber)->firstOrFail();

            $pageData['selectedPatient'] = $patientData;
        }
        $pageData['departmentKey'] = $departmentKey;

        if(!$departmentKey || $departmentKey == ''){

            $pageData['departments'] = ServiceDepartment::all();
            
        }else{

            $isRecesitation = Str::startsWith($departmentKey, 'RECES-');
            $departmentKey = $isRecesitation ? Str::replaceFirst('RECES-', '', $departmentKey) : $departmentKey;
            
            $department = ServiceDepartment::where('slug', $departmentKey)->firstOrFail();

            $pageData['departments'] = ServiceDepartment::all();

            if($isRecesitation){
                $pageData['recesitation'] = true;

                //Get Service orders of patient for this department

                $pageData['existingServiceOrders'] = ServiceOrder::with('service')->where('patient_id', $pageData['selectedPatient']->id)
                    ->where('type', $departmentKey)
                    ->get();

                $pageData['services'] = ServiceRecestation::where('service_department_id', $department->id)->get();
            }else{
                $pageData['services'] = Service::where('service_department_id', $department->id)->get()->map(function($service){
                    $service->available_providers = $service->available_providers;
                    return $service;
                });
            }

        }
        // dd(Service::where('id', 33)->first()->available_providers);
        // dd($pageData['services']);

        return Inertia::render('counter/patient',$pageData);
    }

    public function transactionStore(Request $request)
    {
        $openCounter = Closing::with('transactions')->where('status','open')->where('receptionist_id', request()->user()->id)->first();

        if(!$openCounter){
            return redirect(route('counter-open'));
        }

        $validatedData = $request->validate([
            'income_or_expense' => 'required|in:INCOME,EXPENSE',
        ]);

        if($validatedData['income_or_expense'] == 'EXPENSE'){
            $request->validate([
                'type' => 'required|in:EXP,VOUCHER-PAY',
            ]);

            if($request->get('type') == 'VOUCHER-PAY'){
                $expenseVData = $request->validate([
                    'voucher_id' => 'required|exists:expense_vouchers,id',
                    'payed_to' => 'nullable|exists:users,id',
                    'payed_to_other' => 'nullable|string',
                ]);

                $voucher = ExpenseVoucher::find($expenseVData['voucher_id']);

                $transaction = Transaction::create([
                    'closing_id' => $openCounter->id,
                    'created_by' => $request->user()->id,
                    'type' => TransactionElementType::VOUCHER_PAY,
                    'income_or_expense' => 'EXPENSE',
                    'amount' => $voucher->amount
                ]);

                $expense = Expense::create([
                    'voucher_id' => $voucher->id,
                    'type' => 'CASH',
                    'payed_to' => $request->get('payed_to', 'Other') !== 'Other' ? $expenseVData['payed_to'] : null,
                    'payed_to_other' => $request->get('payed_to', 'Other') === 'Other' ? $expenseVData['payed_to_other'] : null,
                    'amount' => $voucher->amount,
                ]);

                TransactionElement::create([
                    'closing_id' => $openCounter->id,
                    'transaction_id' => $transaction->id,
                    'expense_id' => $expense->id,
                    'exp_voucher_id' => $voucher->id,
                    'created_by' => $request->user()->id,
                    'type' => 'VOUCHER_PAY',
                    'income_or_expense' => 'EXPENSE',
                    'amount' => $voucher->amount,
                ]);

                return redirect()->route('transaction-view',[
                    'tYear' => $transaction->year,
                    'tMonth' => $transaction->month,
                    'tDay' => $transaction->day,
                    'tNumber' => $transaction->number
                ]);


            }else if($request->get('type') == 'EXP'){
                $expenseData = $request->validate([
                    'description' => 'required|string',
                    'amount' => 'required|numeric',
                ]);

                $transaction = Transaction::create([
                    'closing_id' => $openCounter->id,
                    'created_by' => $request->user()->id,
                    'type' => TransactionElementType::EXP,
                    'income_or_expense' => 'EXPENSE',
                    'amount' => $expenseData['amount']
                ]);

                $expense = Expense::create([
                    'description' => $expenseData['description'],
                    'type' => 'CASH',
                    'amount' => $expenseData['amount'],
                ]);

                TransactionElement::create([
                    'closing_id' => $openCounter->id,
                    'transaction_id' => $transaction->id,
                    'expense_id' => $expense->id,
                    'created_by' => $request->user()->id,
                    'type' => TransactionElementType::EXP,
                    'income_or_expense' => 'EXPENSE',
                    'amount' => $expenseData['amount'],
                ]);

                return redirect()->route('transaction-view',[
                    'tYear' => $transaction->year,
                    'tMonth' => $transaction->month,
                    'tDay' => $transaction->day,
                    'tNumber' => $transaction->number
                ]);
            }



        }elseif($validatedData['income_or_expense'] == 'INCOME'){
            $validatedData = $request->validate([
                'income_or_expense' => 'required|in:INCOME,EXPENSE',
                'patient_id' => 'required|exists:patients,id',
                'department_key' => 'required|string',
                'total_amount' => 'required|numeric',
                'payment_method' => 'required|in:CASH,CARD,INSURANCE,OTHER',
                'amount_paid' => 'required|numeric',
                'change_amount' => 'required|numeric',
                'items' => 'array|min:1',
            ]);

            $isRecesitation = Str::startsWith($validatedData['department_key'], 'RECES-');
            $departmentKey = $isRecesitation ? Str::replaceFirst('RECES-', '', $validatedData['department_key']) : $validatedData['department_key'];

            if($isRecesitation){
                // Validate service_order_id
                $request->validate([
                    'service_order_id' => 'required|exists:service_orders,id',
                ]);
            }

            $transaction = Transaction::create([
                'closing_id' => $openCounter->id,
                'created_by' => $request->user()->id,
                'patient_id' => $validatedData['patient_id'],
                'type' => $validatedData['payment_method'],
                'income_or_expense' => 'INCOME',
                'amount' => $validatedData['total_amount'],
                'customer_payed' => $validatedData['amount_paid'],
                'change' => $validatedData['change_amount'],
            ]);

            $orinalTotal = 0;

            foreach($validatedData['items'] as $item){

                $service = !$isRecesitation ? Service::find($item['service_id']) : ServiceRecestation::find($item['service_id']);

                $orinalTotal += $service ? $service->charges * $item['quantity'] : 0;

                TransactionElement::create([
                    'closing_id' => $openCounter->id,
                    'transaction_id' => $transaction->id,
                    'created_by' => $request->user()->id,
                    'patient_id' => $validatedData['patient_id'],
                    'service_id' => !$isRecesitation ? $item['service_id'] : null,
                    'service_recestation_id' => $isRecesitation ? $item['service_id'] : null,
                    'service_order_id' => $isRecesitation ? $request->get('service_order_id', null) : null,
                    'doctor_id' => $item['provider_id'] ?? null,
                    'type' => $request->department_key,
                    'income_or_expense' => 'INCOME',
                    'amount' => $item['total'],
                    'orignal_amount' => $service ? $service->charges * $item['quantity'] : 0,
                ]);
            }

            // Update original amount
            $transaction->orignal_amount = $orinalTotal;
            $transaction->save();

            return redirect()->route('transaction-view',[
                'tYear' => $transaction->year,
                'tMonth' => $transaction->month,
                'tDay' => $transaction->day,
                'tNumber' => $transaction->number
            ]);

        }
    }

    public function transactionView($tYear, $tMonth, $tDay, $tNumber)
    {
        $trNumber = 'TR/'.$tYear.'/'.$tMonth.'/'.$tDay.'/'.$tNumber;


        $transaction = Transaction::with('elements','elements.service','elements.serviceOrder', 'closing','patient')->where('tr_number', $trNumber)->firstOrFail();

        return Inertia::render('transaction',[
            'transaction' => $transaction
        ]);
    }


    public function counterExpense()
    {
        $openCounter = Closing::with('transactions')->where('status','open')->where('receptionist_id', request()->user()->id)->first();

        if(!$openCounter){
            return redirect(route('counter-open'));
        }

        return Inertia::render('counter/expense',[
            'openCounter' => $openCounter,
            'users' => \App\Models\User::all(),
            'categories' => ExpenseCategory::all()
        ]);
    }

    public function hospitalSettings()
    {
        return Inertia::render('admin/hospital-settings');
    }
}
