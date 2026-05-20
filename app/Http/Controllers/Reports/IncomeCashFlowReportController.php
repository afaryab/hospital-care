<?php

namespace App\Http\Controllers\Reports;

use App\Http\Controllers\Controller;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\Service;
use App\Models\TransactionElement;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class IncomeCashFlowReportController extends Controller
{
    /**
     * Generate Income Cash Flow Report
     */
    public function generate(Request $request): Response
    {
        // Increase memory limit for large reports
        ini_set('memory_limit', '2048M');
        ini_set('max_execution_time', '900');
        set_time_limit(900);

        // Force garbage collection
        gc_enable();

        // Get filters from request
        $dateFrom = $request->input('date_from', now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', now()->endOfMonth()->format('Y-m-d'));
        $closingId = $request->input('closing_id');
        $serviceId = $request->input('service_id');
        $serviceOrderId = $request->input('service_order_id');
        $doctorId = $request->input('doctor_id');
        $patientId = $request->input('patient_id');
        $groupBy = $request->input('group_by', 'none');

        // Column visibility
        $columns = [
            'show_date' => $request->boolean('show_date', true),
            'show_transaction_number' => $request->boolean('show_transaction_number', true),
            'show_patient_name' => true,  // Always show patient
            'show_service_name' => true,  // Always show service
            'show_service_order' => $request->boolean('show_service_order', false),  // Hidden by default
            'show_provider_name' => $request->boolean('show_provider_name', false),  // Hidden by default
            'show_original_amount' => $request->boolean('show_original_amount', false),
            'show_edited_amount' => false,  // Don't show edited column, use strikethrough instead
            'show_customer_payed' => false,  // Disabled - not needed
            'show_change' => false,  // Disabled - not needed
            'show_balance' => $request->boolean('show_balance', true),
        ];

        // Add group_by to columns for query optimization
        $columns['group_by'] = $groupBy;

        // Build base query
        $query = $this->buildBaseQuery($dateFrom, $dateTo, $closingId, $serviceId, $serviceOrderId, $doctorId, $patientId, $columns);

        // Check total count first (use faster count query)
        $totalCount = DB::table('transaction_elements')
            ->where('income_or_expense', 'INCOME')
            ->when($dateFrom && $dateTo, function ($q) use ($dateFrom, $dateTo) {
                return $q->whereBetween('created_at', [
                    Carbon::parse($dateFrom)->startOfDay(),
                    Carbon::parse($dateTo)->endOfDay(),
                ]);
            })
            ->when($closingId, fn ($q) => $q->where('closing_id', $closingId))
            ->when($serviceId, fn ($q) => $q->where('service_id', $serviceId))
            ->when($serviceOrderId, fn ($q) => $q->where('service_order_id', $serviceOrderId))
            ->when($doctorId, fn ($q) => $q->where('doctor_id', $doctorId))
            ->when($patientId, fn ($q) => $q->where('patient_id', $patientId))
            ->count();

        // Apply aggressive limit for very large datasets
        $maxRecords = 50000; // Maximum records to process
        $limit = $request->input('limit', $maxRecords);

        if ($totalCount > $maxRecords && ! $request->has('limit')) {
            // Auto-limit to prevent memory issues
            $limit = $maxRecords;
        }

        // Process data using chunking for memory efficiency
        $elements = collect();
        $totals = [
            'count' => 0,
            'total_amount' => 0,
            'total_original_amount' => 0,
            'total_edited' => 0,
            'edited_count' => 0,
            'balance' => 0,
        ];

        $groupedData = [];

        // Use cursor for memory-efficient iteration
        $chunkSize = 1000;
        $processedCount = 0;

        // Use chunk instead of get for memory efficiency
        $query->orderBy('transaction_elements.created_at', 'asc')
            ->limit($limit)
            ->chunk($chunkSize, function ($chunk) use (&$elements, &$totals, &$groupedData, $groupBy, &$processedCount) {
                foreach ($chunk as $element) {
                    // Add to collection
                    $elements->push($element);

                    // Update totals in real-time (faster than array operations)
                    $totals['total_amount'] += $element->amount ?? 0;
                    $totals['total_original_amount'] += $element->orignal_amount ?? 0;

                    if ($element->edited_amount !== null) {
                        $totals['total_edited'] += $element->edited_amount;
                        $totals['edited_count']++;
                    }

                    // Group data if needed
                    if ($groupBy !== 'none') {
                        $this->addToGroup($groupedData, $element, $groupBy);
                    }

                    $processedCount++;

                    // Force garbage collection every 5000 records
                    if ($processedCount % 5000 === 0) {
                        gc_collect_cycles();
                    }
                }

                // Clear chunk from memory
                unset($chunk);
                gc_collect_cycles();
            });

        $totals['count'] = $processedCount;
        $totals['balance'] = $totals['total_amount'];

        // Prepare filter data
        $filterData = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'closing' => $closingId ? ['ct_number' => Closing::where('id', $closingId)->value('ct_number')] : null,
            'service' => $serviceId ? ['name' => Service::where('id', $serviceId)->value('name')] : null,
            'doctor' => $doctorId ? ['name' => User::where('id', $doctorId)->value('name')] : null,
            'patient' => $patientId ? ['ps_number' => Patient::where('id', $patientId)->value('ps_number')] : null,
        ];

        // Prepare data for PDF
        $data = [
            'elements' => $elements,
            'groupedData' => $groupedData,
            'totals' => $totals,
            'filters' => $filterData,
            'group_by' => $groupBy,
            'columns' => $columns,
            'generated_at' => now()->format('M d, Y H:i:s'),
            'total_records_in_db' => $totalCount,
            'records_shown' => $processedCount,
        ];

        // Generate PDF with optimized settings
        $pdf = Pdf::loadView('pdfs.reports.income-cash-flow', $data);
        $pdf->setPaper('a4', 'landscape');

        // Optimize DomPDF settings for large reports
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', false);
        $pdf->setOption('isPhpEnabled', false);
        $pdf->setOption('isFontSubsettingEnabled', false);
        $pdf->setOption('defaultFont', 'DejaVu Sans');
        $pdf->setOption('debugKeepTemp', false);
        $pdf->setOption('debugCss', false);
        $pdf->setOption('debugLayout', false);
        $pdf->setOption('debugLayoutLines', false);
        $pdf->setOption('debugLayoutBlocks', false);
        $pdf->setOption('debugLayoutInline', false);
        $pdf->setOption('debugLayoutPaddingBox', false);
        $pdf->setOption('chroot', resource_path('views'));

        // Check if download is requested
        if ($request->boolean('download')) {
            $filename = 'income-cash-flow-'.now()->format('Y-m-d-His').'.pdf';

            // Clear elements from memory after PDF generation
            unset($elements);
            unset($groupedData);
            gc_collect_cycles();

            return $pdf->download($filename);
        }

        // Stream PDF to browser
        $response = $pdf->stream('income-cash-flow-report.pdf');

        // Clear memory
        unset($elements);
        unset($groupedData);
        unset($pdf);
        gc_collect_cycles();

        return $response;
    }

    /**
     * Build base query with all filters
     *
     * @return Builder
     */
    private function buildBaseQuery($dateFrom, $dateTo, $closingId, $serviceId, $serviceOrderId, $doctorId, $patientId, $columns = [])
    {
        // Base select - always needed columns
        $select = [
            'transaction_elements.id',
            'transaction_elements.amount',
            'transaction_elements.edited_amount',
            'transaction_elements.created_at',
        ];

        // Conditionally add columns based on visibility
        if ($columns['show_original_amount'] ?? false) {
            $select[] = 'transaction_elements.orignal_amount';
        }

        // Add foreign keys for joins
        $needsTransactionJoin = $columns['show_transaction_number'] ?? true;
        $needsPatientJoin = true; // Always show patient
        $needsServiceJoin = true; // Always show service
        $needsServiceOrderJoin = ($columns['show_service_order'] ?? false) || $serviceOrderId;
        $needsDoctorJoin = ($columns['show_provider_name'] ?? false) || $doctorId;

        // For grouping or filtering
        $needsClosingJoin = $closingId || in_array($columns['group_by'] ?? 'none', ['counter']);

        $query = TransactionElement::query()->select($select);

        // Only join tables that are actually needed
        if ($needsTransactionJoin) {
            $query->leftJoin('transactions', 'transaction_elements.transaction_id', '=', 'transactions.id')
                ->addSelect('transactions.tr_number');
        }

        // Always join patients (required field)
        $query->leftJoin('patients', 'transaction_elements.patient_id', '=', 'patients.id')
            ->addSelect('patients.name as ps_name', 'patients.ps_number');

        // Always join services (required field)
        $query->leftJoin('services', 'transaction_elements.service_id', '=', 'services.id')
            ->addSelect('services.name as service_name');

        if ($needsServiceOrderJoin) {
            $query->leftJoin('service_orders', 'transaction_elements.service_order_id', '=', 'service_orders.id')
                ->addSelect('service_orders.so_number');
        }

        if ($needsDoctorJoin) {
            $query->leftJoin('users', 'transaction_elements.doctor_id', '=', 'users.id')
                ->addSelect('users.name as doctor_name');
        }

        if ($needsClosingJoin) {
            $query->leftJoin('closings', 'transaction_elements.closing_id', '=', 'closings.id')
                ->addSelect('closings.ct_number');
        }

        $query->where('transaction_elements.income_or_expense', 'INCOME');

        // Apply date filter
        if ($dateFrom && $dateTo) {
            $query->whereBetween('transaction_elements.created_at', [
                Carbon::parse($dateFrom)->startOfDay(),
                Carbon::parse($dateTo)->endOfDay(),
            ]);
        }

        // Apply closing filter
        if ($closingId) {
            $query->where('transaction_elements.closing_id', $closingId);
        }

        // Apply service filter
        if ($serviceId) {
            $query->where('transaction_elements.service_id', $serviceId);
        }

        // Apply service order filter
        if ($serviceOrderId) {
            $query->where('transaction_elements.service_order_id', $serviceOrderId);
        }

        // Apply doctor filter
        if ($doctorId) {
            $query->where('transaction_elements.doctor_id', $doctorId);
        }

        // Apply patient filter
        if ($patientId) {
            $query->where(function ($q) use ($patientId) {
                $q->where('transaction_elements.patient_id', $patientId)
                    ->orWhere('patients.ps_number', 'like', "%{$patientId}%");
            });
        }

        return $query;
    }

    /**
     * Add element to grouped data
     */
    private function addToGroup(&$groupedData, $element, $groupBy)
    {
        $key = '';
        $label = '';

        switch ($groupBy) {
            case 'counter':
                $key = $element->closing_id ?? 'no-counter';
                $label = $element->ct_number ?? 'No Counter';
                break;

            case 'service':
                $key = $element->service_id ?? 'no-service';
                $label = $element->service_name ?? 'No Service';
                break;

            case 'doctor':
                $key = $element->doctor_id ?? 'no-doctor';
                $label = $element->doctor_name ?? 'No Provider';
                break;

            case 'date':
                $key = Carbon::parse($element->created_at)->format('Y-m-d');
                $label = Carbon::parse($element->created_at)->format('M d, Y');
                break;

            default:
                $key = 'all';
                $label = 'All Records';
        }

        if (! isset($groupedData[$key])) {
            $groupedData[$key] = [
                'label' => $label,
                'items' => [],
                'subtotal' => 0,
                'count' => 0,
            ];
        }

        $groupedData[$key]['items'][] = $element;
        $groupedData[$key]['subtotal'] += $element->edited_amount ?? $element->amount;
        $groupedData[$key]['count']++;
    }
}
