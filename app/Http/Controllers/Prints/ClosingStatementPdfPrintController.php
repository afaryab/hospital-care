<?php

namespace App\Http\Controllers\Prints;

use App\Http\Controllers\Controller;
use App\Models\Closing;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Throwable;

class ClosingStatementPdfPrintController extends Controller
{
    /**
     * Stream PDF for closing statement
     *
     * URL: /PRINT/CT/{year}/{month}/{number}?version=mini|normal
     */
    public function stream(string $year, string $month, string $number, Request $request): Response
    {
        // Generate CT number from URL parameters
        $ctNumber = "CT/{$year}/{$month}/{$number}";

        // Determine report type
        $report = $request->query('report');
        $allowedReports = ['income', 'expense', 'receivables', 'services'];

        // Find the closing record with appropriate eager loading
        $eagerLoads = [
            'reception',
            'receptionist',
            'transactions.elements.patient',
            'transactions.elements.service',
            'transactions.elements.doctor',
        ];

        if ($report === 'receivables' || $report === 'income') {
            $eagerLoads[] = 'transactions.receaveable.patient';
            $eagerLoads[] = 'transactions.receaveable.panel';
        }

        if ($report === 'services') {
            $eagerLoads[] = 'transactions.elements.serviceOrder';
            $eagerLoads[] = 'transactions.elements.serviceRecestation';
            $eagerLoads[] = 'transactions.elements.expenseCategory';
            $eagerLoads[] = 'transactions.elements.expVoucher.payedTo';
        }

        if ($report === 'expense') {
            $eagerLoads[] = 'transactions.elements.expenseCategory';
            $eagerLoads[] = 'transactions.elements.expVoucher.payedTo';
            $eagerLoads[] = 'transactions.elements.expVoucher.expCategory';
        }

        $closing = Closing::where('ct_number', $ctNumber)
            ->with($eagerLoads)
            ->first();

        if (! $closing) {
            abort(404, "Closing statement {$ctNumber} not found");
        }

        // If a specific report type is requested, generate that report
        if ($report && in_array($report, $allowedReports)) {
            $data = $this->prepareClosingData($closing);

            return $this->generateReportPdf($data, $report, $closing);
        }

        // Get version (mini for thermal printer, normal for A4/legal)
        $version = $request->query('variant', 'normal');

        if (! in_array($version, ['mini', 'normal'])) {
            $version = 'normal';
        }

        // Prepare data for PDF
        $data = $this->prepareClosingData($closing);

        // Generate and return PDF (generatePdf now returns Response directly)
        return $this->generatePdf($data, $version);
    }

    /**
     * Prepare closing statement data
     */
    private function prepareClosingData(Closing $closing): array
    {
        // Separate income and expense transactions
        $incomeTransactions = [];
        $expenseTransactions = [];

        $totalIncome = 0;
        $totalExpense = 0;

        $transactonTypesTotals = [];

        $refundCount = 0;
        $editedCount = 0;
        $receaveableCount = 0;

        $incomeByPaymentMethod = [];

        $pannelBills = [];

        foreach ($closing->transactions as $transaction) {
            $transactionData = [
                'id' => $transaction->id,
                'type' => $transaction->type,
                'amount' => $transaction->amount,
                'original_amount' => $transaction->orignal_amount,
                'customer_payed' => $transaction->customer_payed,
                'change' => $transaction->change,
                'edited_amount' => $transaction->edited_amount,
                'created_at' => $transaction->created_at,
                'is_refunded' => $transaction->is_refunded,
                'has_receaveable' => $transaction->receaveable !== null,
                'receaveable_amount' => $transaction->receaveable?->amount,
                'receaveable_patient' => $transaction->receaveable?->patient?->name,
                'receaveable_panel' => $transaction->receaveable?->panel?->name,
                'receaveable_status' => $transaction->receaveable?->status ?? 'PENDING',
                'elements' => [],
            ];

            if ($transaction->income_or_expense === 'INCOME') {
                $transactonTypesTotals[$transaction->type] = ($transactonTypesTotals[$transaction->type] ?? 0) + $transaction->amount;
            } else {
                $transactonTypesTotals['CASH'] = ($transactonTypesTotals['CASH'] ?? 0) - $transaction->amount;
            }

            if ($transaction->is_refunded) {
                $refundCount++;
            }
            if ($transaction->edited_amount > 0) {
                $editedCount++;
            }
            if ($transaction->receaveable_id) {
                $receaveableCount++;
            }

            if ($transaction->panel_id) {
                $panel = $transaction->panel;
                $pannelBills[] = $transaction;
            }

            // Process transaction elements
            foreach ($transaction->elements as $element) {
                $elementData = [
                    'id' => $element->id,
                    'type' => $element->type,
                    'amount' => $element->amount,
                    'original_amount' => $element->orignal_amount,
                    'patient_name' => $element->patient?->name,
                    'patient_ps_number' => $element->patient?->ps_number,
                    'service_name' => $element->service?->name ?? 'N/A',
                    'doctor_name' => $element->doctor?->name,
                    'created_at' => $element->created_at,
                ];

                $transactionData['elements'][] = $elementData;
            }

            // Categorize by income or expense
            if ($transaction->income_or_expense === 'INCOME') {
                $incomeTransactions[] = $transactionData;
                $totalIncome += $transaction->amount;
                $incomeByPaymentMethod[$transaction->type] = ($incomeByPaymentMethod[$transaction->type] ?? 0) + $transaction->amount;
            } else {
                $expenseTransactions[] = $transactionData;
                $totalExpense += $transaction->amount;
            }
        }

        // Calculate totals
        $netAmount = $totalIncome - $totalExpense;

        return [
            'closing' => [
                'ct_number' => $closing->ct_number,
                'status' => $closing->status,
                'opening_amount' => $closing->opening_amount,
                'closing_amount' => $closing->closing_amount,
                'closing_amount_cash' => $closing->closing_amount_cash,
                'closing_amount_cheque' => $closing->closing_amount_cheque,
                'closing_amount_card' => $closing->closing_amount_card,
                'expense_payed' => $closing->expense_payed,
                'cash_receiving_time' => $closing->cash_recieving_time,
                'created_at' => $closing->created_at,
                'updated_at' => $closing->updated_at,
                'year' => $closing->year,
                'month' => $closing->month,
                'number' => $closing->number,
            ],
            'reception' => [
                'name' => $closing->reception?->name ?? 'N/A',
            ],
            'receptionist' => [
                'name' => $closing->receptionist?->name ?? 'N/A',
            ],
            'transactions' => [
                'income' => $incomeTransactions,
                'expense' => $expenseTransactions,
            ],
            'totals' => [
                'total_income' => $totalIncome,
                'total_expense' => $totalExpense,
                'net_amount' => $netAmount,
                'by_type' => $transactonTypesTotals,
                'income_by_payment_method' => $incomeByPaymentMethod,
                'refund_count' => $refundCount,
                'transactions_count' => count($closing->transactions),
                'edited_count' => $editedCount,
                'receaveables_count' => $receaveableCount,
            ],
            'summary' => [
                'income_count' => count($incomeTransactions),
                'expense_count' => count($expenseTransactions),
                'total_transactions' => count($closing->transactions),
            ],
            'generated_at' => Carbon::now(),
        ];
    }

    /**
     * Generate PDF using HTML/CSS
     *
     * @return string
     */
    /**
     * Generate PDF from prepared data
     */
    private function generatePdf(array $data, string $version): Response
    {
        // Use different templates for mini and normal versions
        $viewName = $version === 'mini'
            ? 'pdfs.closing-statement.closing-statement-mini'
            : 'pdfs.closing-statement.closing-statement-normal';

        try {
            // Create PDF with proper paper size and orientation
            $pdf = Pdf::setOption([
                'defaultFont' => 'Helvetica',
            ])->loadView($viewName, $data);

            // Configure PDF based on version
            if ($version === 'mini') {
                // Thermal printer settings (80mm width, auto height)
                $pdf->setPaper([0, 0, 226.77, 500], 'portrait'); // 80mm width, auto-adjusting height
            } else {
                // Standard A4 paper
                $pdf->setPaper('A4', 'portrait');
            }

            // Set PDF options
            // $pdf->setOptions([
            //     'dpi' => 96,
            //     // 'defaultFont' => 'sans-serif',
            //     // 'isRemoteEnabled' => false,
            //     // 'isHtml5ParserEnabled' => true,
            // ]);
            // Generate filename
            $filename = sprintf(
                'closing-statement-%s-%s-%s.pdf',
                $data['closing']['ct_number'],
                $data['closing']['created_at']->format('Y-m-d'),
                $version
            );

            // Stream PDF with appropriate headers
            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public',
            ]);

        } catch (Throwable $e) {
            Log::error('Closing statement PDF generation failed', [
                'ct_number' => $data['closing']['ct_number'] ?? null,
                'version' => $version,
                'message' => $e->getMessage(),
            ]);

            abort(500, 'PDF generation failed');
        }
    }

    /**
     * Generate a specific report PDF (income, expense, receivables, services)
     */
    private function generateReportPdf(array $data, string $report, Closing $closing): Response
    {
        $reportData = array_merge($data, [
            'closing_model' => $closing,
        ]);

        // Build receivables data for the receivables report
        if ($report === 'receivables') {
            $receivables = [];
            $totalReceivables = 0;
            foreach ($closing->transactions as $transaction) {
                if ($transaction->receaveable) {
                    $receivables[] = [
                        'transaction_number' => $transaction->tr_number,
                        'patient_name' => $transaction->receaveable->patient?->name ?? 'N/A',
                        'panel_name' => $transaction->receaveable->panel?->name ?? 'N/A',
                        'amount' => $transaction->receaveable->amount,
                        'orignal_amount' => $transaction->receaveable->orignal_amount ?? $transaction->receaveable->amount,
                        'due_date' => $transaction->receaveable->due_date,
                        'status' => $transaction->receaveable->status ?? 'PENDING',
                        'created_at' => $transaction->created_at,
                    ];
                    $totalReceivables += $transaction->receaveable->amount;
                }
            }
            $reportData['receivables'] = $receivables;
            $reportData['total_receivables'] = $totalReceivables;
        }

        // Build services data grouped by Service → Service Provider, with expenses paid
        if ($report === 'services') {
            // Group income by service → doctor
            $serviceGroups = [];
            $totalServiceIncome = 0;
            foreach ($closing->transactions as $transaction) {
                if ($transaction->income_or_expense !== 'INCOME') {
                    continue;
                }
                foreach ($transaction->elements as $element) {
                    if (! $element->service) {
                        continue;
                    }
                    $serviceName = $element->service->name ?? 'Unknown Service';
                    $doctorName = $element->doctor?->name ?? 'No Provider';
                    $doctorId = $element->doctor_id ?? 0;

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
                            'items' => [],
                            'total_income' => 0,
                            'total_expense' => 0,
                        ];
                    }
                    $serviceGroups[$serviceName]['providers'][$doctorId]['items'][] = [
                        'transaction_number' => $transaction->tr_number,
                        'patient_name' => $element->patient?->name ?? 'N/A',
                        'service_recestation' => $element->serviceRecestation?->name,
                        'amount' => $element->amount,
                        'type' => $transaction->type,
                        'created_at' => $transaction->created_at,
                    ];
                    $serviceGroups[$serviceName]['providers'][$doctorId]['total_income'] += $element->amount;
                    $serviceGroups[$serviceName]['total_income'] += $element->amount;
                    $totalServiceIncome += $element->amount;
                }
            }

            // Collect expenses paid to each doctor from this closing
            $expensesByDoctor = [];
            $totalExpensePaid = 0;
            foreach ($closing->transactions as $transaction) {
                if (! in_array($transaction->income_or_expense, ['EXPENSE', 'VOUCHER-PAY'])) {
                    continue;
                }
                foreach ($transaction->elements as $element) {
                    $payedToId = $element->expVoucher?->payed_to ?? 0;
                    $payedToName = $element->expVoucher?->payedTo?->name ?? $element->expVoucher?->payed_to_name ?? null;
                    if (! $payedToId && ! $payedToName) {
                        continue;
                    }

                    $key = $payedToId ?: ('name:'.$payedToName);
                    if (! isset($expensesByDoctor[$key])) {
                        $expensesByDoctor[$key] = [
                            'doctor_name' => $payedToName ?? 'Unknown',
                            'total' => 0,
                            'items' => [],
                        ];
                    }
                    $expensesByDoctor[$key]['total'] += $element->amount;
                    $expensesByDoctor[$key]['items'][] = [
                        'category' => $element->expenseCategory?->name ?? 'N/A',
                        'voucher' => $element->expVoucher?->vc_number,
                        'amount' => $element->amount,
                    ];
                    $totalExpensePaid += $element->amount;

                    // Also attach to the provider in service groups
                    foreach ($serviceGroups as &$sg) {
                        if (isset($sg['providers'][$payedToId])) {
                            $sg['providers'][$payedToId]['total_expense'] += $element->amount;
                        }
                    }
                    unset($sg);
                }
            }

            // Convert providers arrays from keyed to indexed
            foreach ($serviceGroups as &$sg) {
                $sg['providers'] = array_values($sg['providers']);
            }
            unset($sg);

            $reportData['service_groups'] = array_values($serviceGroups);
            $reportData['expenses_by_doctor'] = array_values($expensesByDoctor);
            $reportData['total_service_income'] = $totalServiceIncome;
            $reportData['total_expense_paid'] = $totalExpensePaid;
        }

        // Build expense details for expense report
        if ($report === 'expense') {
            $expenses = [];
            $totalExpenses = 0;
            foreach ($closing->transactions as $transaction) {
                if (! in_array($transaction->income_or_expense, ['EXPENSE', 'VOUCHER-PAY'])) {
                    continue;
                }
                foreach ($transaction->elements as $element) {
                    // For voucher payments, get category from the voucher itself
                    $categoryName = $element->expenseCategory?->name ?? 'N/A';
                    if ($transaction->income_or_expense === 'VOUCHER-PAY' && $element->expVoucher?->expCategory) {
                        $categoryName = $element->expVoucher->expCategory->name;
                    }

                    $expenses[] = [
                        'transaction_number' => $transaction->tr_number,
                        'category_name' => $categoryName,
                        'voucher_number' => $element->expVoucher?->vc_number,
                        'paid_to' => $element->expVoucher?->payedTo?->name,
                        'amount' => $element->amount,
                        'type' => $transaction->type,
                        'income_or_expense' => $transaction->income_or_expense,
                        'notes' => $transaction->notes,
                        'created_at' => $transaction->created_at,
                    ];
                    $totalExpenses += $element->amount;
                }
            }
            $reportData['expenses'] = $expenses;
            $reportData['total_expenses_detail'] = $totalExpenses;
        }

        $viewName = 'pdfs.closing-statement.report-'.$report;

        try {
            $pdf = Pdf::setOption(['defaultFont' => 'Helvetica'])
                ->loadView($viewName, $reportData);
            $pdf->setPaper('A4', 'portrait');

            $filename = sprintf('%s-report-%s.pdf', $report, $data['closing']['ct_number']);

            return response($pdf->output(), 200, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$filename.'"',
                'Cache-Control' => 'private, max-age=0, must-revalidate',
                'Pragma' => 'public',
            ]);
        } catch (Throwable $e) {
            Log::error("Closing {$report} report PDF generation failed", [
                'ct_number' => $data['closing']['ct_number'] ?? null,
                'report' => $report,
                'message' => $e->getMessage(),
            ]);
            abort(500, 'Report PDF generation failed');
        }
    }

    /**
     * Helper method to format currency
     */
    private function formatCurrency(float $amount): string
    {
        return 'Rs. '.number_format($amount, 2);
    }

    /**
     * Helper method to format date
     */
    private function formatDate(Carbon $date): string
    {
        return $date->format('d/m/Y H:i');
    }
}
