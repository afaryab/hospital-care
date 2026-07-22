<?php

namespace App\Http\Controllers\Reports;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\Closing;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenericReportPdfController extends Controller
{
    public function income(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        $query = TransactionElement::query()
            ->where('income_or_expense', 'INCOME')
            ->where('transaction_elements.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('transaction_elements.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['transaction.closing.reception', 'patient', 'service', 'doctor']);

        if ($request->filled('reception_id')) {
            $query->whereIn('closing_id', Closing::where('reception_id', $request->input('reception_id'))->select('id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        $elements = $query->orderBy('created_at', 'desc')->get();
        $total = $elements->sum('amount');

        // Group by payment type
        $byType = $elements->groupBy(fn ($el) => $el->transaction?->type ?? 'OTHER')
            ->map(fn ($group) => $group->sum('amount'));

        return $this->renderPdf('pdfs.reports.generic-income', [
            'elements' => $elements,
            'total' => $total,
            'by_type' => $byType,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['reception_id', 'type', 'service_id', 'doctor_id']),
        ], 'income-report');
    }

    public function expense(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        $query = TransactionElement::query()
            ->where('income_or_expense', 'EXPENSE')
            ->where('transaction_elements.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('transaction_elements.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['transaction.closing.reception', 'expenseCategory', 'expVoucher.payedTo', 'expVoucher.expCategory']);

        if ($request->filled('reception_id')) {
            $query->whereIn('closing_id', Closing::where('reception_id', $request->input('reception_id'))->select('id'));
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->input('expense_category_id'));
        }

        $elements = $query->orderBy('created_at', 'desc')->get();
        $total = $elements->sum('amount');

        return $this->renderPdf('pdfs.reports.generic-expense', [
            'elements' => $elements,
            'total' => $total,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['reception_id', 'type', 'expense_category_id']),
        ], 'expense-report');
    }

    public function receivables(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        $query = Receaveable::query()
            ->where('receaveables.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('receaveables.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['patient', 'panel', 'transaction']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('panel_id')) {
            $query->where('panel_id', $request->input('panel_id'));
        }

        $items = $query->orderBy('created_at', 'desc')->get();
        $total = $items->sum('amount');

        return $this->renderPdf('pdfs.reports.generic-receivables', [
            'items' => $items,
            'total' => $total,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['status', 'panel_id']),
        ], 'receivables-report');
    }

    public function services(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        // Income elements with services
        $incomeQuery = TransactionElement::query()
            ->whereNotNull('service_id')
            ->where('income_or_expense', 'INCOME')
            ->where('transaction_elements.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('transaction_elements.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['transaction', 'patient', 'service.department', 'doctor']);

        if ($request->filled('reception_id')) {
            $incomeQuery->whereIn('closing_id', Closing::where('reception_id', $request->input('reception_id'))->select('id'));
        }
        if ($request->filled('service_id')) {
            $incomeQuery->where('service_id', $request->input('service_id'));
        }
        if ($request->filled('doctor_id')) {
            $incomeQuery->where('doctor_id', $request->input('doctor_id'));
        }

        $incomeElements = $incomeQuery->get();

        // Expense elements (voucher payments to providers) within span
        $expenseQuery = TransactionElement::query()
            ->where('income_or_expense', 'EXPENSE')
            ->where('transaction_elements.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('transaction_elements.created_at', '<=', DateHelper::dayEndUtc($until))
            ->whereNotNull('exp_voucher_id')
            ->with(['expVoucher.payedTo', 'expVoucher.expCategory', 'expenseCategory']);

        if ($request->filled('reception_id')) {
            $expenseQuery->whereIn('closing_id', Closing::where('reception_id', $request->input('reception_id'))->select('id'));
        }

        $expenseElements = $expenseQuery->get();

        // Build service groups: Service → Provider
        $serviceGroups = [];
        $totalServiceIncome = 0;
        foreach ($incomeElements as $el) {
            $serviceName = $el->service?->name ?? 'Unknown Service';
            $doctorName = $el->doctor?->name ?? 'No Provider';
            $doctorId = $el->doctor_id ?? 0;

            if (! isset($serviceGroups[$serviceName])) {
                $serviceGroups[$serviceName] = [
                    'service_name' => $serviceName,
                    'providers' => [],
                    'total_income' => 0,
                ];
            }
            if (! isset($serviceGroups[$serviceName]['providers'][$doctorId])) {
                $serviceGroups[$serviceName]['providers'][$doctorId] = [
                    'doctor_name' => $doctorName,
                    'doctor_id' => $doctorId,
                    'total_income' => 0,
                    'total_expense' => 0,
                    'items' => [],
                ];
            }
            $serviceGroups[$serviceName]['providers'][$doctorId]['items'][] = [
                'transaction_number' => $el->transaction?->tr_number,
                'patient_name' => $el->patient?->name ?? 'N/A',
                'amount' => $el->amount,
                'type' => $el->transaction?->type ?? $el->type,
                'created_at' => $el->created_at,
            ];
            $serviceGroups[$serviceName]['providers'][$doctorId]['total_income'] += $el->amount;
            $serviceGroups[$serviceName]['total_income'] += $el->amount;
            $totalServiceIncome += $el->amount;
        }

        // Map expenses to providers
        $expensesByDoctor = [];
        $totalExpensePaid = 0;
        foreach ($expenseElements as $el) {
            $payedToId = $el->expVoucher?->payed_to ?? 0;
            $payedToName = $el->expVoucher?->payedTo?->name ?? $el->expVoucher?->payed_to_name ?? null;
            if (! $payedToId && ! $payedToName) {
                continue;
            }

            $key = $payedToId ?: ('name:'.$payedToName);
            if (! isset($expensesByDoctor[$key])) {
                $expensesByDoctor[$key] = [
                    'doctor_name' => $payedToName ?? 'Unknown',
                    'doctor_id' => $payedToId,
                    'total' => 0,
                    'items' => [],
                ];
            }
            $categoryName = $el->expenseCategory?->name ?? $el->expVoucher?->expCategory?->name ?? 'N/A';
            $expensesByDoctor[$key]['total'] += $el->amount;
            $expensesByDoctor[$key]['items'][] = [
                'category' => $categoryName,
                'voucher' => $el->expVoucher?->vc_number,
                'amount' => $el->amount,
                'created_at' => $el->created_at,
            ];
            $totalExpensePaid += $el->amount;

            // Attach to provider in service groups
            foreach ($serviceGroups as &$sg) {
                if (isset($sg['providers'][$payedToId])) {
                    $sg['providers'][$payedToId]['total_expense'] += $el->amount;
                }
            }
            unset($sg);
        }

        // Convert to indexed arrays
        foreach ($serviceGroups as &$sg) {
            $sg['providers'] = array_values($sg['providers']);
        }
        unset($sg);

        return $this->renderPdf('pdfs.reports.generic-services', [
            'service_groups' => array_values($serviceGroups),
            'expenses_by_doctor' => array_values($expensesByDoctor),
            'total_service_income' => $totalServiceIncome,
            'total_expense_paid' => $totalExpensePaid,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['reception_id', 'service_id', 'doctor_id']),
        ], 'services-report');
    }

    public function serviceOrders(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        $query = ServiceOrder::query()
            ->where('service_orders.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('service_orders.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['patient:id,name', 'service:id,name', 'doctor:id,name', 'expenseVouchers'])
            ->withSum(['transactionElements as income_total' => fn ($q) => $q->where('income_or_expense', 'INCOME')], 'amount')
            ->withVoucherExpenseTotal('voucher_total')
            ->withVoucherExpenseTotal('paid_total', fn ($q) => $q->whereNotNull('expense_vouchers.transaction_id')->whereNotNull('expense_vouchers.transaction_element_id'));

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        return $this->renderPdf('pdfs.reports.generic-service-orders', [
            'orders' => $orders,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
        ], 'service-orders-report');
    }

    public function serviceOrder(Request $request, string $id): Response
    {
        $order = ServiceOrder::with(['patient', 'service', 'doctor', 'creator'])
            ->findOrFail($id);

        // Income transaction elements for this SO
        $incomeElements = TransactionElement::where('service_order_id', $order->id)
            ->where('income_or_expense', 'INCOME')
            ->with(['transaction', 'patient'])
            ->orderBy('created_at')
            ->get();

        // Receivables linked via transactions of this SO
        $transactionIds = $incomeElements->pluck('transaction_id')->unique()->filter();
        $receivables = Receaveable::whereIn('transaction_id', $transactionIds)
            ->with(['patient', 'panel', 'transaction'])
            ->get();

        // Transactions paying those receivables
        $receivableIds = $receivables->pluck('id')->filter();
        $receivablePayments = Transaction::whereIn('receaveable_id', $receivableIds)
            ->with(['receaveable.transaction'])
            ->orderBy('created_at')
            ->get();

        // Expense vouchers for this SO — share_amount divides a voucher's
        // amount across every service order it's linked to, so a voucher
        // shared with other orders isn't counted in full against this one.
        $expenseVouchers = $order->expenseVouchers()
            ->withCount('serviceOrders')
            ->with(['expCategory', 'payedTo'])
            ->get()
            ->each(fn ($voucher) => $voucher->share_amount = $voucher->amount / max(1, $voucher->service_orders_count));

        return $this->renderPdf('pdfs.reports.generic-service-order-detail', [
            'order' => $order,
            'incomeElements' => $incomeElements,
            'receivables' => $receivables,
            'receivablePayments' => $receivablePayments,
            'expenseVouchers' => $expenseVouchers,
            'generated_at' => now(),
        ], 'service-order-'.$order->so_short);
    }

    private function renderPdf(string $view, array $data, string $filename): Response
    {
        try {
            $pdf = Pdf::setOption(['defaultFont' => 'Helvetica'])
                ->loadView($view, $data);
            $pdf->setPaper('A4', 'portrait');

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'.pdf"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public',
            ]);
        } catch (Throwable $e) {
            Log::error("Report PDF generation failed: {$view}", [
                'message' => $e->getMessage(),
            ]);
            abort(500, 'Report PDF generation failed');
        }
    }

    /**
     * Resolve the requested [from, until] range as hospital-timezone Carbon
     * instances. User-supplied dates are interpreted in the hospital timezone,
     * and defaults span the current month in that same timezone.
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function resolveDateRange(Request $request): array
    {
        $timezone = DateHelper::timezone();

        $from = $request->filled('from')
            ? Carbon::parse($request->input('from'), $timezone)
            : now($timezone)->startOfMonth();

        $until = $request->filled('until')
            ? Carbon::parse($request->input('until'), $timezone)
            : now($timezone);

        return [$from, $until];
    }

    private function describeFilters(Request $request, array $keys): array
    {
        $filters = [];
        foreach ($keys as $key) {
            if ($request->filled($key)) {
                $filters[$key] = $request->input($key);
            }
        }

        return $filters;
    }

    public function servicePerformance(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        $query = ServiceOrder::query()
            ->where('service_orders.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('service_orders.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['patient:id,name', 'service:id,name', 'doctor:id,name'])
            ->withSum(['transactionElements as income_total' => fn ($q) => $q->where('income_or_expense', 'INCOME')], 'amount')
            ->withVoucherExpenseTotal('voucher_total');

        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }
        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $totalIncome = $orders->sum('income_total');
        $totalExpense = $orders->sum('voucher_total');

        // Group by type/department
        $byDepartment = $orders->groupBy('type')->map(fn ($group) => [
            'count' => $group->count(),
            'income' => $group->sum('income_total'),
            'expense' => $group->sum('voucher_total'),
        ]);

        return $this->renderPdf('pdfs.reports.generic-service-performance', [
            'orders' => $orders,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'by_department' => $byDepartment,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['type', 'status', 'service_id', 'doctor_id']),
        ], 'service-performance-report');
    }

    public function serviceProvider(Request $request): Response
    {
        [$from, $until] = $this->resolveDateRange($request);

        $query = ServiceOrder::query()
            ->where('service_orders.created_at', '>=', DateHelper::dayStartUtc($from))
            ->where('service_orders.created_at', '<=', DateHelper::dayEndUtc($until))
            ->with(['patient:id,name', 'service:id,name', 'doctor:id,name', 'expenseVouchers.expCategory'])
            ->withSum(['transactionElements as income_total' => fn ($q) => $q->where('income_or_expense', 'INCOME')], 'amount')
            ->withVoucherExpenseTotal('voucher_total');

        if ($request->filled('doctor_id')) {
            $query->where('doctor_id', $request->input('doctor_id'));
        }
        if ($request->filled('service_id')) {
            $query->where('service_id', $request->input('service_id'));
        }
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $orders = $query->orderBy('created_at', 'desc')->get();

        $provider = $request->filled('doctor_id')
            ? User::find($request->input('doctor_id'))
            : null;

        $totalIncome = $orders->sum('income_total');
        $totalExpense = $orders->sum('voucher_total');

        return $this->renderPdf('pdfs.reports.generic-service-provider', [
            'orders' => $orders,
            'provider' => $provider,
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'from' => $from,
            'until' => $until,
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['doctor_id', 'service_id', 'status']),
        ], 'service-provider-report');
    }
}
