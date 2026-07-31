<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Closing Statement - {{ $closing['ct_number'] }}</title>
    @php $report_title = 'Closing Statement'; $report_color = '#374151'; @endphp
</head>
<body>
    @include('pdfs.closing-statement.partials.report-header', ['report_title' => $report_title, 'report_color' => $report_color])

    {{-- Additional Closing Details --}}
    <div class="info-grid">
        <div class="info-grid-row">
            <div class="info-cell">
                <span class="info-label">Opening Amount</span><br>
                <span class="info-value">Rs. {{ number_format($closing['opening_amount'], 2) }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Closing Amount</span><br>
                <span class="info-value">Rs. {{ number_format($closing['closing_amount'], 2) }}</span>
            </div>
        </div>
        <div class="info-grid-row">
            <div class="info-cell">
                <span class="info-label">Cash Received</span><br>
                <span class="info-value">{{ $closing['cash_receiving_time'] ? \App\Helpers\DateHelper::pdfFormat($closing['cash_receiving_time'], 'd/m/Y H:i') : 'N/A' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Expense Paid</span><br>
                <span class="info-value">Rs. {{ number_format($closing['expense_payed'], 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Income Transactions — grouped by Service → Service Provider --}}
    @if(count($service_groups) > 0)
        <div class="section-title">Income Transactions ({{ $summary['income_count'] }})</div>
        @foreach($service_groups as $group)
            <div class="sub-section">
                {{ $group['service_name'] }}
                <span style="float: right; font-size: 10px; font-weight: 400;">
                    Service Total: {{ number_format($group['total_income'], 2) }}
                </span>
            </div>

            @foreach($group['providers'] as $provider)
                <table>
                    <thead>
                        <tr>
                            <th colspan="4" style="background: #ffffff; text-transform: none; font-size: 10px; font-weight: 600; padding: 3px 8px;">
                                {{ $provider['doctor_name'] }}
                            </th>
                        </tr>
                        <tr>
                            <th style="width: 42px;">Time</th>
                            <th>Patient</th>
                            <th style="width: 60px;">Method</th>
                            <th class="amount" style="width: 80px;">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($provider['items'] as $item)
                        <tr>
                            <td>{{ \App\Helpers\DateHelper::pdfFormat($item['created_at'], 'H:i') }}</td>
                            <td>{{ $item['patient_name'] }}</td>
                            <td><span class="mono">{{ $item['type'] }}</span></td>
                            <td class="amount">{{ number_format($item['amount'], 2) }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="subtotal-row">
                            <td colspan="3" class="text-right">Subtotal &mdash; {{ $provider['doctor_name'] }}</td>
                            <td class="amount">{{ number_format($provider['total_income'], 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            @endforeach
        @endforeach
        <table>
            <tbody>
                <tr class="total-row">
                    <td class="text-right">Total Income</td>
                    <td class="amount" style="width: 80px;">{{ number_format($totals['total_income'], 2) }}</td>
                </tr>
            </tbody>
        </table>
    @endif

    {{-- Expense Transactions --}}
    @if(count($transactions['expense']) > 0)
    <div class="section-title">Expense Transactions ({{ $summary['expense_count'] }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 42px;">Time</th>
                <th>Category / Description</th>
                <th class="amount" style="width: 80px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions['expense'] as $transaction)
                @foreach($transaction['elements'] as $element)
                @php $isDiscount = ($element['expense_category_type'] ?? '') === 'DISC'; @endphp
                <tr @if($isDiscount) style="background: #fefce8;" @endif>
                    <td>{{ \App\Helpers\DateHelper::pdfFormat($element['created_at'], 'H:i') }}</td>
                    <td>
                        @if($element['expense_category_name'])
                            <span @if($isDiscount) style="color: #854d0e; font-weight: 700;" @endif>
                                {{ $element['expense_category_name'] }}
                            </span>
                            @if($isDiscount)
                                <span class="badge" style="background: #fef9c3; color: #854d0e; margin-left: 4px;">DISCOUNT</span>
                            @endif
                        @else
                            {{ $element['type'] }}
                        @endif
                        @if($element['patient_name'])
                            — {{ $element['patient_name'] }}
                        @endif
                    </td>
                    <td class="amount" @if($isDiscount) style="color: #854d0e;" @endif>{{ number_format($element['amount'], 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="2" class="text-right">Total Expenses</td>
                <td class="amount">{{ number_format($totals['total_expense'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Income by Payment Method --}}
    @if(!empty($totals['income_by_payment_method']))
    <div class="section-title">Income by Payment Method</div>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="amount" style="width: 120px;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($totals['income_by_payment_method'] as $method => $amount)
            <tr>
                <td>{{ ucwords(strtolower(str_replace('_', ' ', $method))) }}</td>
                <td class="amount">{{ number_format($amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-right">Total Income</td>
                <td class="amount">{{ number_format($totals['total_income'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Receivables Created --}}
    @if(count($receivables['created']) > 0)
    <div class="section-title" style="background: #7c3aed;">Receivables Created ({{ count($receivables['created']) }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 42px;">Time</th>
                <th>Patient</th>
                <th>Panel</th>
                <th style="width: 80px;">Status</th>
                <th class="amount" style="width: 80px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receivables['created'] as $row)
            <tr>
                <td>{{ \App\Helpers\DateHelper::pdfFormat($row['created_at'], 'H:i') }}</td>
                <td>{{ $row['patient_name'] }}</td>
                <td>{{ $row['panel_name'] }}</td>
                <td><span class="badge badge-purple">{{ strtoupper($row['status']) }}</span></td>
                <td class="amount">{{ number_format($row['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Receivables Created</td>
                <td class="amount">{{ number_format($totals['receivables_created_total'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Receivables Collected --}}
    @if(count($receivables['collected']) > 0)
    <div class="section-title" style="background: #059669;">Receivables Collected ({{ count($receivables['collected']) }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 42px;">Time</th>
                <th>Patient</th>
                <th style="width: 80px;">Method</th>
                <th class="amount" style="width: 80px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receivables['collected'] as $row)
            <tr>
                <td>{{ \App\Helpers\DateHelper::pdfFormat($row['created_at'], 'H:i') }}</td>
                <td>{{ $row['patient_name'] }}</td>
                <td><span class="badge badge-green">{{ $row['method'] }}</span></td>
                <td class="amount">{{ number_format($row['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Receivables Collected</td>
                <td class="amount">{{ number_format($totals['receivables_collected_total'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Summary --}}
    @php
        $cashIncome = $totals['income_by_payment_method']['CASH'] ?? 0;
        $chequeIncome = $totals['income_by_payment_method']['CHEQUE'] ?? 0;
        $bankTransferIncome = $totals['income_by_payment_method']['BANK_TRANSFER'] ?? 0;
        $cardIncome = $totals['income_by_payment_method']['CARD'] ?? 0;
        $netCash = $cashIncome - $totals['total_expense'];
        $receivablesTotal = 0;
        $receivablesCount = 0;
        foreach(array_merge($transactions['income'], $transactions['expense']) as $tr) {
            if (!empty($tr['has_receaveable']) && $tr['receaveable_amount'] > 0) {
                $receivablesTotal += $tr['receaveable_amount'];
                $receivablesCount++;
            }
        }
    @endphp
    <div class="section-title">Summary</div>
    <table>
        <tr>
            <td>Cash Income</td>
            <td class="amount" style="width: 120px; color: #065f46;">{{ number_format($cashIncome, 2) }}</td>
        </tr>
        @if($chequeIncome > 0)
        <tr>
            <td>Cheque Income</td>
            <td class="amount" style="color: #065f46;">{{ number_format($chequeIncome, 2) }}</td>
        </tr>
        @endif
        @if($bankTransferIncome > 0)
        <tr>
            <td>Bank Transfer Income</td>
            <td class="amount" style="color: #065f46;">{{ number_format($bankTransferIncome, 2) }}</td>
        </tr>
        @endif
        @if($cardIncome > 0)
        <tr>
            <td>Card Income</td>
            <td class="amount" style="color: #065f46;">{{ number_format($cardIncome, 2) }}</td>
        </tr>
        @endif
        <tr>
            <td>Expense Paid</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($totals['total_expense'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Net Total</td>
            <td class="amount">{{ number_format($totals['net_amount'], 2) }}</td>
        </tr>
        <tr style="background: #eff6ff;">
            <td>Net Cash <span class="text-muted" style="font-size: 9px;">(Cash Income − Expenses)</span></td>
            <td class="amount" style="color: #1d4ed8;">{{ number_format($netCash, 2) }}</td>
        </tr>
        @if($receivablesTotal > 0)
        <tr style="background: #faf5ff;">
            <td>Receivables Outstanding <span class="text-muted" style="font-size: 9px;">({{ $receivablesCount }} items)</span></td>
            <td class="amount" style="color: #7c3aed;">{{ number_format($receivablesTotal, 2) }}</td>
        </tr>
        @endif
        @if($totals['receivables_created_total'] > 0)
        <tr style="background: #faf5ff;">
            <td>Receivables Created</td>
            <td class="amount" style="color: #7c3aed;">{{ number_format($totals['receivables_created_total'], 2) }}</td>
        </tr>
        @endif
        @if($totals['receivables_collected_total'] > 0)
        <tr style="background: #ecfdf5;">
            <td>Receivables Collected</td>
            <td class="amount" style="color: #059669;">{{ number_format($totals['receivables_collected_total'], 2) }}</td>
        </tr>
        @endif
    </table>

    @include('pdfs.closing-statement.partials.report-footer', ['report_title' => $report_title])
</body>
</html>