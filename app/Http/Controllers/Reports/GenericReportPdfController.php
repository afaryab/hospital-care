<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Closing;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
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
        $from = $request->date('from') ?? now()->startOfMonth();
        $until = $request->date('until') ?? now();

        $query = TransactionElement::query()
            ->where('income_or_expense', 'INCOME')
            ->whereDate('transaction_elements.created_at', '>=', $from)
            ->whereDate('transaction_elements.created_at', '<=', $until)
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
            'from' => Carbon::parse($from),
            'until' => Carbon::parse($until),
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['reception_id', 'type', 'service_id', 'doctor_id']),
        ], 'income-report');
    }

    public function expense(Request $request): Response
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $until = $request->date('until') ?? now();

        $query = TransactionElement::query()
            ->where('income_or_expense', 'EXPENSE')
            ->whereDate('transaction_elements.created_at', '>=', $from)
            ->whereDate('transaction_elements.created_at', '<=', $until)
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
            'from' => Carbon::parse($from),
            'until' => Carbon::parse($until),
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['reception_id', 'type', 'expense_category_id']),
        ], 'expense-report');
    }

    public function receivables(Request $request): Response
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $until = $request->date('until') ?? now();

        $query = Receaveable::query()
            ->whereDate('receaveables.created_at', '>=', $from)
            ->whereDate('receaveables.created_at', '<=', $until)
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
            'from' => Carbon::parse($from),
            'until' => Carbon::parse($until),
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['status', 'panel_id']),
        ], 'receivables-report');
    }

    public function services(Request $request): Response
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $until = $request->date('until') ?? now();

        // Income elements with services
        $incomeQuery = TransactionElement::query()
            ->whereNotNull('service_id')
            ->where('income_or_expense', 'INCOME')
            ->whereDate('transaction_elements.created_at', '>=', $from)
            ->whereDate('transaction_elements.created_at', '<=', $until)
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
            ->whereDate('transaction_elements.created_at', '>=', $from)
            ->whereDate('transaction_elements.created_at', '<=', $until)
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
            'from' => Carbon::parse($from),
            'until' => Carbon::parse($until),
            'generated_at' => now(),
            'filters' => $this->describeFilters($request, ['reception_id', 'service_id', 'doctor_id']),
        ], 'services-report');
    }

    public function serviceOrders(Request $request): Response
    {
        $from = $request->date('from') ?? now()->startOfMonth();
        $until = $request->date('until') ?? now();

        $query = ServiceOrder::query()
            ->whereDate('service_orders.created_at', '>=', $from)
            ->whereDate('service_orders.created_at', '<=', $until)
            ->with(['patient', 'service', 'doctor', 'expenseVouchers']);

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

        // Compute aggregated totals per order
        foreach ($orders as $order) {
            $order->income_total = $order->transactionElements()
                ->where('income_or_expense', 'INCOME')->sum('amount');
            $order->voucher_total = $order->expenseVouchers->sum('amount');
            $order->paid_total = $order->expenseVouchers
                ->filter(fn ($v) => $v->status === 'payed')->sum('amount');
        }

        return $this->renderPdf('pdfs.reports.generic-service-orders', [
            'orders' => $orders,
            'from' => Carbon::parse($from),
            'until' => Carbon::parse($until),
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

        // Expense vouchers for this SO
        $expenseVouchers = $order->expenseVouchers()
            ->with(['expCategory', 'payedTo'])
            ->get();

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
}
