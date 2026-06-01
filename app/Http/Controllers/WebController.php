<?php

namespace App\Http\Controllers;

use App\Enum\CounterStatus;
use App\Enum\TransactionElementType;
use App\Models\Closing;
use App\Models\ExpenseCategory;
use App\Models\ExpenseVoucher;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\PatientManager;
use App\Models\PaymentMethod;
use App\Models\Receaveable;
use App\Models\Reception;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\ServiceRecestation;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use App\Services\BreachDetectionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Inertia\Inertia;

class WebController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        $dashboard = [
            'roles' => [
                'isReceptionist' => $user->isReceptionist(),
                'isDoctor' => $user->isAnyDoctor(),
                'isAdmin' => $user->isAdmin(),
                'isAccountant' => $user->isAccountant(),
            ],
            'receptionist' => $user->isReceptionist()
                ? $this->buildReceptionistDashboard($user)
                : null,
            'doctor' => $user->isAnyDoctor()
                ? $this->buildDoctorDashboard($user)
                : null,
        ];

        return Inertia::render('dashboard', $dashboard);
    }

    private function buildReceptionistDashboard(User $user): array
    {
        $openCounter = Closing::query()
            ->where('status', 'open')
            ->where('receptionist_id', $user->id)
            ->with('reception:id,name')
            ->latest('id')
            ->first();

        $lastClosed = Closing::query()
            ->where('status', '!=', 'open')
            ->where('receptionist_id', $user->id)
            ->with('reception:id,name')
            ->latest('id')
            ->first();

        $reference = $openCounter ?? $lastClosed;

        $income = 0.0;
        $expense = 0.0;
        $balance = 0.0;
        $netCash = null;
        if ($reference) {
            $income = (float) Transaction::query()
                ->where('closing_id', $reference->id)
                ->where('income_or_expense', 'INCOME')
                ->sum('amount');
            $expense = (float) Transaction::query()
                ->where('closing_id', $reference->id)
                ->where('income_or_expense', 'EXPENSE')
                ->sum('amount');
            $balance = (float) ($reference->opening_amount ?? 0) + $income - $expense;
            $netCash = $openCounter
                ? null
                : (float) ($reference->closing_amount ?? $balance);
        }

        return [
            'has_open_counter' => (bool) $openCounter,
            'counter' => $reference ? [
                'id' => $reference->id,
                'ct_number' => $reference->ct_number,
                'year' => $reference->year,
                'month' => $reference->month,
                'number' => $reference->number,
                'status' => $reference->status,
                'reception_name' => $reference->reception?->name,
                'opening_amount' => (float) ($reference->opening_amount ?? 0),
                'closing_amount' => $reference->closing_amount !== null
                    ? (float) $reference->closing_amount
                    : null,
                'opened_at' => optional($reference->created_at)->toIso8601String(),
                'closed_at' => optional($reference->closed_at)->toIso8601String(),
            ] : null,
            'totals' => [
                'income' => $income,
                'expense' => $expense,
                'balance' => $balance,
                'net_cash' => $netCash,
            ],
        ];
    }

    private function buildDoctorDashboard(User $user): array
    {
        $base = ServiceOrder::query()->where('doctor_id', $user->id);

        $byStatus = (clone $base)
            ->selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy(DB::raw('LOWER(status)'))
            ->pluck('total', 'status');

        $today = (clone $base)
            ->whereDate('created_at', now()->toDateString())
            ->count();

        $recent = (clone $base)
            ->with(['patient:id,name,ps_number', 'service:id,name'])
            ->latest('id')
            ->limit(5)
            ->get()
            ->map(fn (ServiceOrder $so) => [
                'id' => $so->id,
                'so_number' => $so->so_number,
                'token_short' => $so->token_short,
                'status' => $so->status,
                'patient_name' => $so->patient?->name,
                'ps_number' => $so->patient?->ps_number,
                'service_name' => $so->service?->name,
                'created_at' => optional($so->created_at)->toIso8601String(),
            ]);

        return [
            'counts' => [
                'open' => (int) ($byStatus['open'] ?? 0),
                'in_progress' => (int) ($byStatus['in-progress'] ?? 0),
                'treated' => (int) ($byStatus['treated'] ?? 0),
                'closed' => (int) ($byStatus['closed'] ?? 0),
                'refunded' => (int) ($byStatus['refunded'] ?? 0),
                'cancelled' => (int) ($byStatus['cancelled'] ?? 0),
                'today' => $today,
            ],
            'recent' => $recent,
        ];
    }

    public function myPatients(Request $request)
    {
        $user = $request->user();

        if (! $user->isAnyDoctor()) {
            abort(403, 'Doctors only.');
        }

        $filters = $request->only(['status', 'q', 'from', 'until']);

        $query = ServiceOrder::query()
            ->where('doctor_id', $user->id)
            ->with(['patient:id,name,ps_number', 'service:id,name']);

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }
        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('so_number', 'like', "%{$q}%")
                    ->orWhere('so_short', 'like', "%{$q}%")
                    ->orWhereHas('patient', fn ($p) => $p
                        ->where('name', 'like', "%{$q}%")
                        ->orWhere('ps_number', 'like', "%{$q}%"));
            });
        }
        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (! empty($filters['until'])) {
            $query->whereDate('created_at', '<=', $filters['until']);
        }

        $orders = $query->latest('id')->paginate(20)->withQueryString();

        $stats = ServiceOrder::query()
            ->where('doctor_id', $user->id)
            ->selectRaw('LOWER(status) as status, COUNT(*) as total')
            ->groupBy(DB::raw('LOWER(status)'))
            ->pluck('total', 'status');

        return Inertia::render('doctor/my-patients', [
            'orders' => $orders,
            'filters' => $filters,
            'stats' => [
                'open' => (int) ($stats['open'] ?? 0),
                'in_progress' => (int) ($stats['in-progress'] ?? 0),
                'treated' => (int) ($stats['treated'] ?? 0),
                'closed' => (int) ($stats['closed'] ?? 0),
                'refunded' => (int) ($stats['refunded'] ?? 0),
                'total' => (int) array_sum($stats->toArray()),
            ],
        ]);
    }

    public function myPayments(Request $request)
    {
        $user = $request->user();

        if (! $user->isAnyDoctor()) {
            abort(403, 'Doctors only.');
        }

        $filters = $request->only(['status', 'q', 'from', 'until']);

        $query = ExpenseVoucher::query()
            ->where('payed_to', $user->id)
            ->with(['expCategory:id,name', 'serviceOrder:id,so_number,patient_id', 'serviceOrder.patient:id,name,ps_number']);

        if (! empty($filters['q'])) {
            $q = $filters['q'];
            $query->where(function ($sub) use ($q) {
                $sub->where('vc_number', 'like', "%{$q}%")
                    ->orWhere('notes', 'like', "%{$q}%");
            });
        }
        if (! empty($filters['from'])) {
            $query->whereDate('created_at', '>=', $filters['from']);
        }
        if (! empty($filters['until'])) {
            $query->whereDate('created_at', '<=', $filters['until']);
        }

        $vouchers = $query->latest('id')->paginate(20)->withQueryString();

        $paidTotal = (float) ExpenseVoucher::query()
            ->where('payed_to', $user->id)
            ->whereNotNull('transaction_id')
            ->whereNotNull('transaction_element_id')
            ->sum('amount');

        $pendingTotal = (float) ExpenseVoucher::query()
            ->where('payed_to', $user->id)
            ->where(function ($q) {
                $q->whereNull('transaction_id')->orWhereNull('transaction_element_id');
            })
            ->sum('amount');

        return Inertia::render('doctor/my-payments', [
            'vouchers' => $vouchers,
            'filters' => $filters,
            'totals' => [
                'paid' => $paidTotal,
                'pending' => $pendingTotal,
                'total' => $paidTotal + $pendingTotal,
            ],
        ]);
    }

    public function register(Request $request, $year = false, $month = false)
    {
        $user = $request->user();
        $query = Patient::query();

        // Patient Managers only see patients they are explicitly authorised to.
        // All other roles (doctors, nurses, accountants, admin, receptionists)
        // see the full register.
        if ($user->isPatientManager() && ! $user->isAdmin() && ! $user->isAccountant() && ! $user->isReceptionist() && ! $user->isAnyDoctor() && ! $user->nursingStaffProfiles()->exists()) {
            $authorisedIds = PatientManager::where('user_id', $user->id)->pluck('patient_id');
            $query->whereIn('id', $authorisedIds);
        }

        $year && $query->whereYear('created_at', $year);
        $month && $query->whereMonth('created_at', $month);

        $data = $query->orderBy('created_at', 'DESC')->paginate(8);

        $serviceDepartments = ServiceDepartment::all();

        return Inertia::render('register', [
            'yearSelected' => $year,
            'monthSelected' => $month,
            'patientsPaginated' => $data,
            'serviceDepartments' => $serviceDepartments,
        ]);

    }

    public function patient(Request $request, $year, $month, $number, $departmentKey = false, $serviceNumber = false)
    {

        $psNumber = 'PS/'.$year.'/'.$month.'/'.$number;

        $patientData = Patient::with('treatments')->where('ps_number', $psNumber)->firstOrFail();

        $serviceDepartments = ServiceDepartment::all();
        $serviceOrder = null;

        if ($serviceNumber) {

            $soNumber = 'PS/'.$year.'/'.$month.'/'.$number.'/'.$departmentKey.'/'.$serviceNumber;

            $serviceOrder = ServiceOrder::where('so_number', $soNumber)
                ->orWhere('so_short', $soNumber)
                ->firstOrFail();

        }

        app(BreachDetectionService::class)->recordPatientAccess($request->user(), $patientData, $request);

        return Inertia::render('patient', [
            'departmentKey' => $departmentKey,
            'patientData' => $patientData,
            'serviceDepartments' => $serviceDepartments,
            'serviceOrder' => $serviceOrder,
        ]);

    }

    public function treatment($year, $month, $number, $departmentKey, $treatment)
    {

        $psNumber = 'PS/'.$year.'/'.$month.'/'.$number;

        $patientData = Patient::where('ps_number', $psNumber)->firstOrFail();

        return Inertia::render('patient', [
            'departmentKey' => $departmentKey,
            'treatmentKey' => $treatment,
            'patientData' => $patientData,
        ]);

    }

    public function counter(Request $request)
    {
        $openCounter = Closing::where('status', 'open')->where('receptionist_id', $request->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        } else {
            return redirect(route('counter-view', [
                'ctYear' => $openCounter->year,
                'ctMonth' => $openCounter->month,
                'ctNumber' => $openCounter->number,
            ]));
        }
    }

    public function userCountersList($year = false, $month = false)
    {
        $query = Closing::query();

        $query->where('receptionist_id', request()->user()->id);

        $year && $query->whereYear('created_at', $year);
        $month && $query->whereMonth('created_at', $month);

        $data = $query->latest('id')->paginate(8);

        return Inertia::render('counter/list', [
            'yearSelected' => $year,
            'monthSelected' => $month,
            'closings' => $data,
        ]);
    }

    public function counterOpen(Request $request)
    {
        $openCounter = Closing::where('status', 'open')->where('receptionist_id', $request->user()->id)->first();

        if ($openCounter) {
            return redirect(route('counter-view', [
                'ctYear' => $openCounter->year,
                'ctMonth' => $openCounter->month,
                'ctNumber' => $openCounter->number,
            ]));
        } else {
            $receptionIds = $request->user()
                ->receptionistProfiles()
                ->whereNotNull('reception_id')
                ->pluck('reception_id');

            $receptions = $receptionIds->isNotEmpty()
                ? Reception::whereIn('id', $receptionIds)->get()
                : Reception::all();

            return Inertia::render('counter/open', [
                'recptions' => $receptions,
            ]);
        }
    }

    public function counterStore(Request $request)
    {

        $data = $request->validate([
            'opening_balance' => 'nullable|numeric',
            'reception_id' => 'required|exists:receptions,id',
        ]);

        $counter = Closing::create([
            'reception_id' => $data['reception_id'],
            'receptionist_id' => $request->user()->id,
            'ct_number' => Closing::generateCounterNumber(),
            'status' => CounterStatus::OPEN,
            'opening_amount' => $data['opening_balance'] ?? 0,
        ]);

        return redirect(route('counter-view', [
            'ctYear' => $counter->year,
            'ctMonth' => $counter->month,
            'ctNumber' => $counter->number,
        ]));
    }

    public function counterClose(Request $request)
    {
        $openCounter = Closing::where('status', 'open')->where('receptionist_id', $request->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        } else {

            if ($request->isMethod('post')) {

                // Sum elements amounts
                $totalAmount = $openCounter->transactions()->sum('amount');
                $openCounter->closing_amount = $totalAmount;
                // Close the counter
                $openCounter->status = CounterStatus::CLOSED;
                $openCounter->closed_at = now();
                $openCounter->save();

                return redirect(route('counter-view', [
                    'ctYear' => $openCounter->year,
                    'ctMonth' => $openCounter->month,
                    'ctNumber' => $openCounter->number,
                ]));
            }

            $totalIncAmount = $openCounter->transactions()->where('income_or_expense', 'INCOME')->sum('amount');
            $totalExpAmount = $openCounter->transactions()->where('income_or_expense', 'EXPENSE')->sum('amount');
            $openCounter->closing_amount = $totalIncAmount - $totalExpAmount;
            $openCounter->expense_payed = $totalExpAmount;
            $openCounter->save();

            return Inertia::render('counter/close', [
                'openCounter' => $openCounter,
            ]);
        }

    }

    public function counterView($ctYear, $ctMonth, $ctNumber, Request $request)
    {

        $ctNumber = 'CT/'.$ctYear.'/'.$ctMonth.'/'.$ctNumber;

        $openCounter = Closing::with('transactions', 'transactions.receaveable', 'transactions.receaveable.patient', 'transactions.receaveable.panel', 'transactions.patient', 'transactions.elements', 'transactions.patient', 'transactions.elements.service', 'transactions.elements.serviceRecestation', 'transactions.elements.serviceOrder', 'transactions.elements.expenseCategory', 'transactions.elements.expVoucher', 'transactions.elements.doctor', 'transactions.elements.refundedTransaction')->where('ct_number', $ctNumber)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        }

        $this->authorize('view', $openCounter);

        return Inertia::render('counter/view', [
            'openCounter' => $openCounter,
        ]);
    }

    public function counterPatient($pYear = false, $pMonth = false, $number = false, $departmentKey = false)
    {
        $openCounter = Closing::where('status', 'open')->where('receptionist_id', request()->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        }
        $pageData = [
            'openCounter' => $openCounter,
        ];

        if ($pYear || $pMonth || $number) {

            $psNumber = 'PS/'.$pYear.'/'.$pMonth.'/'.$number;

            $patientData = Patient::with('treatments', 'transactions', 'transactions.elements', 'transactions.elements.service', 'transactions.elements.serviceOrder', 'receaveables')->where('ps_number', $psNumber)->firstOrFail();

            $pageData['selectedPatient'] = $patientData;
        }
        $pageData['departmentKey'] = $departmentKey;

        if (! $departmentKey || $departmentKey == '') {

            $pageData['departments'] = ServiceDepartment::all();

        } else {

            // $pageData['panels'] = Panel::all();

            $isRecesitation = Str::startsWith($departmentKey, 'RECES-');
            $departmentKey = $isRecesitation ? Str::replaceFirst('RECES-', '', $departmentKey) : $departmentKey;

            $department = ServiceDepartment::where('slug', $departmentKey)->firstOrFail();

            $pageData['departments'] = ServiceDepartment::all();

            if ($isRecesitation) {
                $pageData['recesitation'] = true;

                // Get Service orders of patient for this department

                $pageData['existingServiceOrders'] = ServiceOrder::with('service')->where('patient_id', $pageData['selectedPatient']->id)
                    ->where('type', $departmentKey)
                    ->get();

                $pageData['services'] = ServiceRecestation::where('service_department_id', $department->id)->get();
            } else {
                $pageData['services'] = Service::where('service_department_id', $department->id)->get();

                $providerTypes = $pageData['services']->pluck('service_provider_types')->flatten()->unique()->filter();

                $userIds = collect([]);

                foreach ($providerTypes as $providerType) {
                    // Skip invalid classes to avoid errors
                    if (! class_exists($providerType)) {
                        continue;
                    }

                    // Build a base query selecting only user_id to keep memory usage low
                    $userIds = $userIds->merge($providerType::query()->select('user_id')->pluck('user_id')->toArray());

                    $pageData['providers'][$providerType] = User::whereIn('id', $userIds)->get();

                }

                // dd($pageData['services']->map(function($service){
                //     if(!$service->have_service_provider || empty($service->service_provider_types)) {
                //         dd($service);
                //     }
                //     return [
                //         'id' => $service->id,
                //         'name' => $service->name,
                //         'available_providers' => $service->serviceProviders()->get(),
                //     ];
                // }));
            }

        }
        // dd(Service::where('id', 33)->first()->available_providers);
        // dd($pageData['services']);

        $pageData['panelCompanies'] = Panel::all();
        $pageData['paymentMethods'] = PaymentMethod::all();

        return Inertia::render('counter/income', $pageData);
    }

    public function transactionStore(Request $request)
    {
        $openCounter = Closing::with('transactions')->where('status', 'open')->where('receptionist_id', request()->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        }

        $validatedData = $request->validate([
            'income_or_expense' => 'required|in:INCOME,EXPENSE',
        ]);

        if ($validatedData['income_or_expense'] == 'EXPENSE') {

            $request->validate([
                'type' => 'required|in:EXP,VOUCHER-PAY',
            ]);

            $reception = $openCounter->reception;

            if ($request->get('type') == 'VOUCHER-PAY' && $reception && ! $reception->is_allowed_to_pay_voucher) {
                return back()->withErrors(['message' => 'This reception is not allowed to pay vouchers.']);
            }

            if ($request->get('type') == 'EXP' && $reception && ! $reception->is_allowed_to_pay_from_petty_cash) {
                return back()->withErrors(['message' => 'This reception is not allowed to pay from petty cash.']);
            }

            if ($request->get('type') == 'VOUCHER-PAY') {
                $expenseVData = $request->validate([
                    'voucher_id' => 'required|exists:expense_vouchers,id',
                    'payed_to' => 'nullable|exists:users,id',
                    'payed_to_other' => 'nullable|string',
                ]);

                DB::beginTransaction();

                try {

                    $voucher = ExpenseVoucher::find($expenseVData['voucher_id']);

                    $transaction = Transaction::create([
                        'closing_id' => $openCounter->id,
                        'created_by' => $request->user()->id,
                        'type' => TransactionElementType::VOUCHER_PAY,
                        'income_or_expense' => 'EXPENSE',
                        'amount' => $voucher->amount,
                    ]);

                    $transactionElement = TransactionElement::create([
                        'closing_id' => $openCounter->id,
                        'transaction_id' => $transaction->id,
                        'exp_voucher_id' => $voucher->id,
                        'created_by' => $request->user()->id,
                        'type' => 'VOUCHER_PAY',
                        'income_or_expense' => 'EXPENSE',
                        'amount' => $voucher->amount,
                    ]);

                    $voucher->update([
                        'transaction_id' => $transaction->id,
                        'transaction_element_id' => $transactionElement->id,
                    ]);

                } catch (\Exception $e) {
                    DB::rollBack();

                    return back()->withErrors(['message' => 'An error occurred while processing the transaction. Please try again.']);
                }

                DB::commit();

                return redirect()->route('transaction-view', [
                    'tYear' => $transaction->year,
                    'tMonth' => $transaction->month,
                    'tDay' => $transaction->day,
                    'tNumber' => $transaction->number,
                ]);

            } elseif ($request->get('type') == 'EXP') {

                $expenseData = $request->validate([
                    'amount' => 'required|numeric',
                    'category_id' => 'required|exists:expense_categories,id',
                    'payed_to' => 'nullable',
                ]);
                if (empty($expenseData['payed_to'])) {
                    $expenseData['payed_to'] = 'Other';
                }
                if ($expenseData['payed_to'] == 'Other' && empty($expenseData['payed_to_other'])) {
                    $array = $request->validate([
                        'payed_to_other' => 'required|string',
                    ]);
                    $expenseData['payed_to_other'] = $array['payed_to_other'];
                } else {
                    $array = $request->validate([
                        'payed_to' => 'exists:users,id',
                    ]);
                    $expenseData['payed_to'] = $array['payed_to'];
                }

                $expenseCategory = ExpenseCategory::find($expenseData['category_id']);

                $isRefund = $expenseCategory->type == 'RFND' || $expenseCategory->name == 'Refund';

                $isDiscount = $expenseCategory->type == 'DISC' || $expenseCategory->name == 'Discount';

                $isDoctorFilePayment = $expenseCategory->name == 'Inpatient Doctor Payment';

                if (($isRefund || $isDiscount) && empty($expenseData['transaction_id'])) {
                    $array = $request->validate([
                        'transaction_id' => 'required|exists:transactions,id',
                    ]);

                    $expenseData['transaction_id'] = $array['transaction_id'];
                }

                if ($isDoctorFilePayment && empty($expenseData['file_number'])) {
                    $array = $request->validate([
                        'file_number' => 'required|exists:service_orders,id|or_exists:service_orders,so_number',
                    ]);

                    $expenseData['file_number'] = $array['file_number'];
                }

                if ($isRefund || $isDiscount) {

                    $refundedTransaction = Transaction::find($expenseData['transaction_id']);

                    if (! $refundedTransaction) {
                        $refundedTransaction = Transaction::where('tr_number', $expenseData['transaction_id'])->first();
                    }
                }

                if ($isDoctorFilePayment) {
                    $ServiceOrderExp = ServiceOrder::find($expenseData['file_number']);
                    if (! $ServiceOrderExp) {
                        $fileNumber = $expenseData['file_number'];
                        $ServiceOrderExp = ServiceOrder::where('so_number', $fileNumber)
                            ->orWhere('so_short', $fileNumber)
                            ->first();
                    }
                }

                $array = $request->validate([
                    'description' => 'required|string',
                ]);

                $expenseData['description'] = $array['description'];

                DB::beginTransaction();

                try {

                    $transaction = Transaction::create([
                        'closing_id' => $openCounter->id,
                        'created_by' => $request->user()->id,
                        'type' => 'CASH',
                        'income_or_expense' => 'EXPENSE',
                        'expense_category_id' => $expenseData['category_id'] ?? null,
                        'notes' => $expenseData['description'] ?? null,
                        'amount' => $expenseData['amount'],
                        'is_refunded' => $isRefund,
                    ]);

                    TransactionElement::create([
                        'closing_id' => $openCounter->id,
                        'transaction_id' => $transaction->id,
                        'created_by' => $request->user()->id,
                        'type' => TransactionElementType::PETTY_CASH,
                        'income_or_expense' => 'EXPENSE',
                        'expense_category_id' => $expenseData['category_id'] ?? null,
                        'amount' => $expenseData['amount'],
                        'refunded_transaction_id' => ($isRefund || $isDiscount) ? ($refundedTransaction->id ?? null) : null,
                        'expense_service_order_id' => $isDoctorFilePayment ? $ServiceOrderExp->id : null,
                    ]);

                    if ($isRefund && $refundedTransaction) {
                        $refundedTransaction->is_refunded = 1;
                        $refundedTransaction->save();
                    }

                    // Adjust receivable when discounting or refunding a transaction that has an unpaid receivable
                    if (($isRefund || $isDiscount) && ! empty($refundedTransaction)) {
                        $refundedTransaction->load('receaveable');
                        $receaveable = $refundedTransaction->receaveable;

                        if ($receaveable && ! in_array($receaveable->status, ['PAID', 'cancelled'], true)) {
                            $newAmount = max(0, $receaveable->amount - $expenseData['amount']);

                            $receaveable->amount = $newAmount;

                            if ($newAmount <= 0) {
                                $hasPaymentTransactions = Transaction::where('receaveable_id', $receaveable->id)->exists();

                                if ($hasPaymentTransactions) {
                                    $receaveable->delete();
                                } else {
                                    $receaveable->status = 'cancelled';
                                    $receaveable->save();
                                }
                            } else {
                                $receaveable->save();
                            }
                        }
                    }

                } catch (\Exception $e) {
                    // Log exception
                    Log::error('Expense Record Failed '.$e->getMessage());
                    DB::rollBack();

                    return back()->withErrors(['message' => 'An error occurred while processing the transaction. Please try again.']);
                }

                DB::commit();

                return redirect()->route('transaction-view', [
                    'tYear' => $transaction->year,
                    'tMonth' => $transaction->month,
                    'tDay' => $transaction->day,
                    'tNumber' => $transaction->number,
                ]);
            }

        } elseif ($validatedData['income_or_expense'] == 'INCOME') {

            $validatedData = $request->validate([
                'income_or_expense' => 'required|in:INCOME,EXPENSE',
                'patient_id' => 'required|exists:patients,id',
                'department_key' => 'required|string',
                'total_amount' => 'required|numeric',
                'payment_method' => 'required|in:CASH,CARD,PANEL,CHEQUE,BANK_TRANSFER',
                'panel_company' => 'required_if:payment_method,PANEL',
                'amount_paid' => 'required|numeric',
                'items' => 'array|min:1',
            ]);

            $validatedData['change_amount'] = $validatedData['amount_paid'] - $validatedData['total_amount'];

            if ($validatedData['payment_method'] === 'PANEL') {
                $request->validate([
                    'panel_company' => 'exists:panels,id',
                ]);
            }

            $isRecesitation = Str::startsWith($validatedData['department_key'], 'RECES-');
            $departmentKey = $isRecesitation ? Str::replaceFirst('RECES-', '', $validatedData['department_key']) : $validatedData['department_key'];

            // Enforce allowed_departments for this reception
            $reception = $openCounter->reception;
            if ($reception && ! empty($reception->allowed_departments) && ! in_array($departmentKey, $reception->allowed_departments)) {
                return back()->withErrors(['message' => "This reception is not allowed to process transactions for the {$departmentKey} department."]);
            }

            if ($isRecesitation) {
                // Validate service_order_id
                $request->validate([
                    'service_order_id' => 'required|exists:service_orders,id',
                ]);
            }

            DB::beginTransaction();

            try {

                $transaction = Transaction::create([
                    'closing_id' => $openCounter->id,
                    'created_by' => $request->user()->id,
                    'patient_id' => $validatedData['patient_id'],
                    'type' => $validatedData['payment_method'],
                    'income_or_expense' => 'INCOME',
                    'panel_id' => $validatedData['payment_method'] === 'PANEL' ? $validatedData['panel_company'] : null,
                    'amount' => (
                        $validatedData['amount_paid'] === 0 ? 0 : (
                            $validatedData['total_amount'] > $validatedData['amount_paid'] ? $validatedData['amount_paid'] : $validatedData['amount_paid'] - $validatedData['change_amount'])
                    ),
                    'customer_payed' => $validatedData['amount_paid'],
                    'change' => $validatedData['change_amount'] > 0 ? $validatedData['change_amount'] : 0,
                ]);

                $orinalTotal = 0;

                foreach ($validatedData['items'] as $item) {

                    $service = ! $isRecesitation ? Service::find($item['service_id']) : ServiceRecestation::find($item['service_id']);

                    $orinalTotal += $service ? $service->charges * $item['quantity'] : 0;

                    TransactionElement::create([
                        'closing_id' => $openCounter->id,
                        'transaction_id' => $transaction->id,
                        'created_by' => $request->user()->id,
                        'patient_id' => $validatedData['patient_id'],
                        'service_id' => ! $isRecesitation ? $item['service_id'] : null,
                        'service_recestation_id' => $isRecesitation ? $item['service_id'] : null,
                        'service_order_id' => $isRecesitation ? $request->get('service_order_id', null) : null,
                        'doctor_id' => $item['provider_id'] ?? null,
                        'type' => $request->department_key,
                        'panel_id' => $validatedData['payment_method'] === 'PANEL' ? $validatedData['panel_company'] : null,
                        'income_or_expense' => 'INCOME',
                        'amount' => $item['total'],
                        'orignal_amount' => $service ? $service->charges * $item['quantity'] : 0,
                    ]);
                }

                // Update original amount
                $transaction->orignal_amount = $orinalTotal;
                $transaction->save();

                if ($validatedData['change_amount'] < 0) {
                    // Create receaveable for the remaining amount
                    $receaveableAmount = abs($validatedData['change_amount']);

                    Receaveable::create([
                        'patient_id' => $validatedData['patient_id'],
                        'transaction_id' => $transaction->id,
                        'amount' => $receaveableAmount,
                        'orignal_amount' => $receaveableAmount,
                        'status' => 'unpaid',
                        'panel_id' => $validatedData['payment_method'] === 'PANEL' ? $validatedData['panel_company'] : null,
                    ]);
                }

            } catch (\Exception $e) {
                Log::error($e->getMessage());
                DB::rollBack();

                return back()->withErrors(['message' => 'An error occurred while processing the transaction. Please try again.']);
            }

            DB::commit();

            return redirect()->route('transaction-view', [
                'tYear' => $transaction->year,
                'tMonth' => $transaction->month,
                'tDay' => $transaction->day,
                'tNumber' => $transaction->number,
            ]);

        }
    }

    public function receaveablesPayment(Request $request)
    {

        $openCounter = Closing::with('transactions')->where('status', 'open')->where('receptionist_id', request()->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        }

        $validatedData = $request->validate([
            'receaveable_id' => 'required|exists:receaveables,id',
            'payment_method' => 'required|in:CASH,CARD,PANEL,CHEQUE,BANK_TRANSFER',
            'panel_id' => 'required_if:payment_method,PANEL|exists:panels,id',
            'amount_to_collect' => 'required|numeric|gt:0',
            'note' => 'nullable|string',
        ]);

        $receaveable = Receaveable::with('transaction')->findOrFail($validatedData['receaveable_id']);

        $transaction = $receaveable->transaction;

        if ($transaction->elements->count() !== 1) {
            return redirect()->back()->withErrors(['error' => 'Invalid receaveable transaction elements.']);
        }

        DB::beginTransaction();

        try {

            // A receivable settlement is a cash/accounts-receivable movement, not new
            // service revenue: the full service amount was already recognised on the
            // original transaction's element. Recording only the payment Transaction
            // (Dr Cash / Cr A/R, see AbacusClosingService) keeps the shift cash totals
            // correct while avoiding a duplicate INCOME TransactionElement that would
            // double-count the income report and the service order's totals when the
            // receivable is collected within the same shift.
            $newTransaction = Transaction::create([
                'closing_id' => $openCounter->id,
                'created_by' => $request->user()->id,
                'patient_id' => $receaveable->patient_id,
                'type' => $validatedData['payment_method'],
                'income_or_expense' => 'INCOME',
                'amount' => $validatedData['amount_to_collect'],
                'panel_id' => $validatedData['payment_method'] === 'PANEL' ? $validatedData['panel_id'] : null,
                'receaveable_id' => $receaveable->id,
                'notes' => $validatedData['note'] ?? null,
            ]);

            $receaveable->amount -= $validatedData['amount_to_collect'];
            if ($receaveable->amount <= 0) {
                $receaveable->status = 'paid';
                $receaveable->amount = 0;
            }
            $receaveable->save();

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()->withErrors(['error' => 'An error occurred while processing the payment: '.$e->getMessage()]);
        }

        DB::commit();

        return redirect()->route('transaction-view', [
            'tYear' => $newTransaction->year,
            'tMonth' => $newTransaction->month,
            'tDay' => $newTransaction->day,
            'tNumber' => $newTransaction->number,
        ]);

    }

    public function transactionView($tYear = null, $tMonth = null, $tDay = null, $tNumber = null)
    {
        if (! $tYear || ! $tMonth || ! $tDay || ! $tNumber) {
            return Inertia::render('transaction/search');
        }
        $trNumber = 'TR/'.$tYear.'/'.$tMonth.'/'.$tDay.'/'.$tNumber;

        $transaction = Transaction::with('elements', 'elements.service', 'elements.serviceOrder', 'closing', 'patient', 'closing.reception', 'patient.receaveables')->where('tr_number', $trNumber)->firstOrFail();

        $this->authorize('view', $transaction);

        return Inertia::render('transaction/view', [
            'transaction' => $transaction,
        ]);
    }

    public function transactionEdit($tYear = null, $tMonth = null, $tDay = null, $tNumber = null)
    {
        if (! $tYear || ! $tMonth || ! $tDay || ! $tNumber) {
            return Inertia::render('transaction/search');
        }
        $trNumber = 'TR/'.$tYear.'/'.$tMonth.'/'.$tDay.'/'.$tNumber;

        $transaction = Transaction::with('elements', 'elements.service', 'elements.serviceOrder', 'closing', 'patient', 'closing.reception', 'patient.receaveables')->where('tr_number', $trNumber)->firstOrFail();

        $this->authorize('update', $transaction);

        return Inertia::render('transaction/edit', [
            'transaction' => $transaction,
        ]);
    }

    public function transactionUpdate(Request $request)
    {
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'type' => 'nullable|in:CASH,CARD,INSURANCE,OTHER',
            'amount' => 'nullable|numeric',
            'customer_payed' => 'nullable|numeric',
            'change' => 'nullable|numeric',
            'elements' => 'nullable|array',
            'elements.*.id' => 'required|exists:transaction_elements,id',
            'elements.*.amount' => 'nullable|numeric',
            'elements.*.doctor_id' => 'nullable|exists:users,id',
            'elements.*.note' => 'nullable|string',
        ]);

        $transaction = Transaction::with('elements')->findOrFail($validated['transaction_id']);

        $this->authorize('update', $transaction);

        if (array_key_exists('type', $validated) && $validated['type'] !== null) {
            $transaction->type = $validated['type'];
        }
        if (array_key_exists('amount', $validated) && $validated['amount'] !== null) {
            $transaction->amount = $validated['amount'];
        }
        if (array_key_exists('customer_payed', $validated) && $validated['customer_payed'] !== null) {
            $transaction->customer_payed = $validated['customer_payed'];
        }
        if (array_key_exists('change', $validated) && $validated['change'] !== null) {
            $transaction->change = $validated['change'];
        }
        $transaction->save();

        if (! empty($validated['elements'])) {
            foreach ($validated['elements'] as $elementData) {
                $element = TransactionElement::find($elementData['id']);
                if (! $element) {
                    continue;
                }

                if (array_key_exists('amount', $elementData) && $elementData['amount'] !== null) {
                    $element->amount = $elementData['amount'];
                }
                if (array_key_exists('doctor_id', $elementData)) {
                    $element->doctor_id = $elementData['doctor_id'];
                }
                if (array_key_exists('note', $elementData)) {
                    $element->note = $elementData['note'];
                }
                $element->save();
            }
        }

        return redirect()->route('transaction-view', [
            'tYear' => $transaction->year,
            'tMonth' => $transaction->month,
            'tDay' => $transaction->day,
            'tNumber' => $transaction->number,
        ])->with('success', 'Transaction updated');
    }

    public function receaveables()
    {
        $openCounter = Closing::with('transactions')->where('status', 'open')->where('receptionist_id', request()->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        }

        $receaveables = Receaveable::with('patient', 'transaction')->where('status', 'unpaid')->paginate();

        return Inertia::render('counter/receaveables', [
            'openCounter' => $openCounter,
            'receaveables' => $receaveables,
            'paymentMethods' => PaymentMethod::all(),
            'panelCompanies' => Panel::all(),
        ]);
    }

    public function counterExpense(Request $request)
    {
        $openCounter = Closing::with('transactions')->where('status', 'open')->where('receptionist_id', request()->user()->id)->first();

        if (! $openCounter) {
            return redirect(route('counter-open'));
        }

        $paymentTypeInUrl = $request->get('type', null);

        $categoryNameInUrl = $request->get('category', null);

        $categoryIdInUrl = $request->get('category_id', null);

        $amountInUrl = $request->get('amount', null);

        $doctorIdInUrl = $request->get('doctor_id', null);

        $transactionIdInUrl = $request->get('transaction_id', null);

        $transactionNumberInUrl = $request->get('transaction_number', null);

        $payedToOtherInUrl = $request->get('payed_to_other', null);

        $voucherIdInUrl = $request->get('voucher_id', null);

        $expenseCategory = null;

        if ($categoryNameInUrl && $categoryIdInUrl) {
            $expenseCategory = ExpenseCategory::find($categoryIdInUrl);
        } else {
            if ($categoryNameInUrl) {
                $expenseCategory = ExpenseCategory::firstOrCreate(['name' => $categoryNameInUrl]);
            } elseif ($categoryIdInUrl) {
                $expenseCategory = ExpenseCategory::find($categoryIdInUrl);
            } else {
                $expenseCategory = null;
            }
        }

        $doctor = null;

        if ($doctorIdInUrl) {
            $doctor = User::find($doctorIdInUrl);
        }

        $trnsaction = null;

        if ($transactionIdInUrl && $transactionNumberInUrl) {
            $trnsaction = Transaction::find($transactionIdInUrl);
        } else {
            if ($transactionNumberInUrl) {
                $trnsaction = Transaction::where('tr_number', $transactionNumberInUrl)->first();
            } elseif ($transactionIdInUrl) {
                $trnsaction = Transaction::find($transactionIdInUrl);
            } else {
                $trnsaction = null;
            }
        }

        $voucher = null;

        if ($voucherIdInUrl) {
            $voucher = ExpenseVoucher::with('payedTo')->find($voucherIdInUrl);
        }

        // dd([
        //         'type' => $paymentTypeInUrl,
        //         'amount' => $amountInUrl,
        //         'category' => $expenseCategory,
        //         'doctor' => $doctor,
        //         'transaction' => $trnsaction,
        //         'payed_to_other' => $payedToOtherInUrl,
        //     ]);

        $expenseCategories = ExpenseCategory::query()
            ->where('allow_petty_cash', true)
            ->whereNotIn('name', ['Outdoor Doctors Payments'])
            ->get();

        return Inertia::render('counter/expense', [
            'openCounter' => $openCounter,
            'users' => User::all(),
            'categories' => $expenseCategories,
            'selected' => [
                'type' => $paymentTypeInUrl,
                'amount' => $amountInUrl,
                'category' => $expenseCategory,
                'doctor' => $doctor,
                'transaction' => $trnsaction,
                'payed_to_other' => $payedToOtherInUrl,
                'voucher' => $voucher,
            ],
        ]);
    }

    public function serviceOrdersOverview(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'max:50'],
            'type' => ['nullable', 'string', 'in:OPD,IND,EMG,DNT,LAB,ULT,RAD'],
            'service_order_id' => ['nullable', 'integer', 'exists:service_orders,id'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        $serviceOrdersQuery = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name,icon',
                'doctor:id,name',
            ])
            ->withSum(['transactionElements as income_total' => function ($query) {
                $query->where('income_or_expense', 'INCOME');
            }], 'amount')
            ->withSum(['transactionElements as expense_total' => function ($query) {
                $query->where('income_or_expense', 'EXPENSE');
            }], 'amount')
            ->withSum('expenseVouchers as voucher_expense_total', 'amount')
            ->latest('id');

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $serviceOrdersQuery->where(function ($query) use ($search) {
                $query->where('so_number', 'like', "%{$search}%")
                    ->orWhere('so_short', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn ($patientQuery) => $patientQuery
                        ->where('name', 'like', "%{$search}%")
                        ->orWhere('ps_number', 'like', "%{$search}%")
                    )
                    ->orWhereHas('doctor', fn ($doctorQuery) => $doctorQuery->where('name', 'like', "%{$search}%"))
                    ->orWhereIn('service_recestation_id', ServiceRecestation::query()
                        ->where('name', 'like', "%{$search}%")
                        ->pluck('id')
                    )
                    ->orWhereHas('transactionElements.transaction', fn ($trQuery) => $trQuery->where('tr_number', 'like', "%{$search}%"));
            });
        }

        if (! empty($filters['status'])) {
            $serviceOrdersQuery->where('status', $filters['status']);
        }

        if (! empty($filters['type'])) {
            $serviceOrdersQuery->where('type', $filters['type']);
        }

        $serviceOrders = $serviceOrdersQuery
            ->paginate(20)
            ->withQueryString();

        $selectedServiceOrder = null;
        if (! empty($filters['service_order_id'])) {
            $selectedServiceOrder = ServiceOrder::query()
                ->with([
                    'patient:id,name,ps_number,contact,cnic',
                    'service:id,name,icon',
                    'doctor:id,name',
                    'transactionElements.transaction:id,tr_number,created_at,type',
                    'transactionElements.expenseCategory:id,name',
                    'transactionElements.serviceRecestation:id,name',
                    'transactionElements.expVoucher:id,vc_number',
                    'expenseVouchers:id,vc_number,exp_category_id,amount,payed_to,payed_to_name,transaction_id,transaction_element_id,created_at',
                    'expenseVouchers.expCategory:id,name',
                    'treatmentRecord:id,service_order_id,diagnosis_text,treatment_plan,outcome,treated_at,is_finalized',
                    'treatmentRecord.treatingDoctor:id,name',
                    'treatmentRecord.vitalSigns:id,treatment_record_id,temperature,blood_pressure_systolic,blood_pressure_diastolic,pulse_rate,respiratory_rate,oxygen_saturation,recorded_at',
                ])
                ->withSum(['transactionElements as income_total' => function ($query) {
                    $query->where('income_or_expense', 'INCOME');
                }], 'amount')
                ->withSum(['transactionElements as expense_total' => function ($query) {
                    $query->where('income_or_expense', 'EXPENSE');
                }], 'amount')
                ->withSum('expenseVouchers as voucher_expense_total', 'amount')
                ->find($filters['service_order_id']);

            // Load receivables for the selected service order
            if ($selectedServiceOrder) {
                $incomeTransactionIds = $selectedServiceOrder->transactionElements
                    ->where('income_or_expense', 'INCOME')
                    ->pluck('transaction_id')
                    ->unique()
                    ->filter();

                $selectedServiceOrder->setRelation(
                    'receivables',
                    Receaveable::whereIn('transaction_id', $incomeTransactionIds)
                        ->with(['patient:id,name', 'panel:id,name', 'transaction:id,tr_number'])
                        ->get()
                );
            }
        }

        return Inertia::render('service-orders/index', [
            'serviceOrders' => $serviceOrders,
            'selectedServiceOrder' => $selectedServiceOrder,
            'filters' => [
                'search' => $filters['search'] ?? '',
                'status' => $filters['status'] ?? '',
                'type' => $filters['type'] ?? '',
            ],
        ]);
    }

    public function updateServiceOrderStatus(Request $request, ServiceOrder $serviceOrder): RedirectResponse
    {
        $validated = $request->validate([
            'status' => ['required', 'string', 'in:OPEN,CLOSED,IN-PROGRESS'],
        ]);

        $serviceOrder->status = $validated['status'];
        $serviceOrder->save();

        return back();
    }

    public function opdQueue()
    {
        $type = 'OPD';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 50)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            // Filter out null keys (orders without a service) just in case
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,
        ]);
    }

    public function indoorQueue()
    {

        $type = 'IND';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 15)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            // Filter out null keys (orders without a service) just in case
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,
        ]);
    }

    public function emergencyQueue()
    {
        $type = 'EMER';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 50)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,
        ]);
    }

    public function dentalQueue()
    {
        $type = 'DNT';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 50)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,
        ]);
    }

    public function laboratoryQueue()
    {
        $type = 'DNT';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 50)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,
        ]);
    }

    public function ultrasoundQueue()
    {
        $type = 'ULT';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 50)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,
        ]);
    }

    public function radiologyQueue()
    {
        $type = 'RAD';
        // Optimized: top 50 per service using window function via derived table (MySQL 8+ disallows HAVING on window alias)
        $base = ServiceOrder::query()
            ->select(['id', 'service_id', 'patient_id', 'created_at', 'status', 'type', 'so_number'])
            ->selectRaw('ROW_NUMBER() OVER (PARTITION BY service_id ORDER BY created_at ASC) AS rn')
            ->where('type', $type)
            ->whereIn('status', ['open', 'in-progress', 'OPEN', 'IN-PROGRESS']);

        // Filter by rn in an outer query
        $ids = DB::query()
            ->fromSub($base, 't')
            ->where('t.rn', '<=', 50)
            ->orderBy('t.service_id')
            ->orderBy('t.created_at', 'ASC')
            ->pluck('id');

        $serviceOrdersByService = ServiceOrder::query()
            ->with([
                'patient:id,name,ps_number',
                'service:id,name',
            ])
            ->whereIn('id', $ids)
            ->orderBy('service_id')
            ->orderBy('created_at', 'ASC')
            ->get()
            ->groupBy('service_id')
            ->filter(fn ($items, $serviceId) => ! is_null($serviceId) && $items->isNotEmpty());

        // Get department services
        $serviceIds = $serviceOrdersByService->keys()->filter()->values();
        $services = Service::whereIn('id', $serviceIds)->get()->keyBy('id');

        return Inertia::render('hospital/opd-queue', [
            'serviceOrdersByService' => $serviceOrdersByService,
            'services' => $services,

        ]);
    }

    public function hospitalSettings()
    {
        return Inertia::render('admin/hospital-settings');
    }

    public function vouchersList(Request $request)
    {
        $query = ExpenseVoucher::with(['expCategory', 'payedTo']);

        if ($year = $request->get('year')) {
            $query->whereYear('created_at', $year);
        }

        if ($month = $request->get('month')) {
            $query->whereMonth('created_at', $month);
        }

        $vouchers = $query->orderBy('created_at', 'DESC')->paginate(15);

        return Inertia::render('counter/vouchers-list', [
            'vouchers' => $vouchers,
            'yearSelected' => $request->get('year', '0'),
            'monthSelected' => $request->get('month', '0'),
        ]);
    }

    public function newVoucher()
    {

        $expenseCategories = ExpenseCategory::query()
            ->where('allow_voucher', true)
            ->where('pay_doc', false)
            ->where('pay_users', false)
            ->get();

        $users = User::where(function ($query) {
            $query->whereHas('opdDoctorProfiles')
                ->orWhereHas('indDoctorProfiles')
                ->orWhereHas('emergencyDoctorProfiles')
                ->orWhereHas('dentistProfiles')
                ->orWhereHas('ultrasoundDoctorProfiles');
        })->get();

        return Inertia::render('counter/new-voucher', [
            'categories' => $expenseCategories,
            'users' => $users,

        ]);
    }

    public function newVoucherForDoctor()
    {

        $expenseCategories = ExpenseCategory::query()
            ->where('allow_voucher', true)
            ->where('pay_doc', true)
            ->get();

        $users = User::where(function ($query) {
            $query->whereHas('opdDoctorProfiles')
                ->orWhereHas('indDoctorProfiles')
                ->orWhereHas('emergencyDoctorProfiles')
                ->orWhereHas('dentistProfiles')
                ->orWhereHas('ultrasoundDoctorProfiles');
        })->get();

        return Inertia::render('counter/new-doctor-voucher', [
            'categories' => $expenseCategories,
            'users' => $users,

        ]);
    }

    public function newVoucherForUser()
    {

        $expenseCategories = ExpenseCategory::query()
            ->where('allow_voucher', true)
            ->where('pay_users', true)
            ->get();

        $users = User::where(function ($query) {
            $query->whereHas('opdDoctorProfiles')
                ->orWhereHas('indDoctorProfiles')
                ->orWhereHas('emergencyDoctorProfiles')
                ->orWhereHas('dentistProfiles')
                ->orWhereHas('ultrasoundDoctorProfiles');
        })->get();

        return Inertia::render('counter/new-user-voucher', [
            'categories' => $expenseCategories,
            'users' => $users,

        ]);
    }

    public function storeVoucher(Request $request)
    {
        $validated = $request->validate([
            'exp_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'payed_to' => ['required', 'integer', 'exists:users,id'],
            'service_order_ids' => ['nullable', 'array'],
            'service_order_ids.*' => ['required', 'integer', 'exists:service_orders,id'],
            'amount' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'description' => ['nullable', 'string', 'max:1000'],
        ]);

        $category = ExpenseCategory::findOrFail($validated['exp_category_id']);
        $serviceOrderIds = $validated['service_order_ids'] ?? [];

        if (! $category->allow_voucher) {
            return back()->withErrors([
                'exp_category_id' => 'The selected expense category is not allowed for voucher payments.',
            ]);
        }

        if ($category->pay_doc && ! $category->pay_others && ! $category->pay_users && empty($serviceOrderIds)) {
            return back()->withErrors([
                'service_order_ids' => 'Service orders are required for this category.',
            ]);
        }

        if (! empty($serviceOrderIds)) {
            // Backend validation: all service orders must be CLOSED
            $serviceOrders = ServiceOrder::whereIn('id', $serviceOrderIds)->get();

            $notClosed = $serviceOrders->filter(fn ($so) => $so->status !== 'CLOSED');
            if ($notClosed->isNotEmpty()) {
                return back()->withErrors([
                    'service_order_ids' => 'All selected service orders must have CLOSED status. Invalid: '.$notClosed->pluck('so_number')->implode(', '),
                ]);
            }
        }

        $voucher = DB::transaction(function () use ($validated, $serviceOrderIds) {
            $voucher = ExpenseVoucher::create([
                'exp_category_id' => $validated['exp_category_id'],
                'payed_to' => $validated['payed_to'],
                'amount' => $validated['amount'],
                'notes' => $validated['description'] ?? null,
            ]);

            if (! empty($serviceOrderIds)) {
                $voucher->serviceOrders()->attach($serviceOrderIds);
            }

            return $voucher;
        });

        return redirect()->route('counter-expense')->with('success', "Voucher {$voucher->vc_number} created successfully.");
    }
}
