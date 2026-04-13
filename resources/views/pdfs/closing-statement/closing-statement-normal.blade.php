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
                <span class="info-value">{{ $closing['cash_receiving_time'] ? \Carbon\Carbon::parse($closing['cash_receiving_time'])->format('d/m/Y H:i') : 'N/A' }}</span>
            </div>
            <div class="info-cell">
                <span class="info-label">Expense Paid</span><br>
                <span class="info-value">Rs. {{ number_format($closing['expense_payed'], 2) }}</span>
            </div>
        </div>
    </div>

    {{-- Income Transactions --}}
    @if(count($transactions['income']) > 0)
    <div class="section-title">Income Transactions ({{ $summary['income_count'] }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 42px;">Time</th>
                <th>Patient</th>
                <th>Service</th>
                <th class="amount" style="width: 80px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions['income'] as $transaction)
                @foreach($transaction['elements'] as $element)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</td>
                    <td>
                        @if($element['patient_name'])
                            {{ $element['patient_name'] }}
                            @if($element['patient_ps_number'])
                                <br><span class="text-muted mono">{{ $element['patient_ps_number'] }}</span>
                            @endif
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $element['service_name'] }}</td>
                    <td class="amount">{{ number_format($element['amount'], 2) }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="3" class="text-right">Total Income</td>
                <td class="amount">{{ number_format($totals['total_income'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Expense Transactions --}}
    @if(count($transactions['expense']) > 0)
    <div class="section-title">Expense Transactions ({{ $summary['expense_count'] }})</div>
    <table>
        <thead>
            <tr>
                <th style="width: 42px;">Time</th>
                <th>Description</th>
                <th class="amount" style="width: 80px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions['expense'] as $transaction)
                @foreach($transaction['elements'] as $element)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($element['created_at'])->format('H:i') }}</td>
                    <td>
                        {{ $element['type'] }}
                        @if($element['patient_name'])
                            — {{ $element['patient_name'] }}
                        @endif
                    </td>
                    <td class="amount">{{ number_format($element['amount'], 2) }}</td>
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

    {{-- Summary --}}
    <div class="section-title">Summary</div>
    <table>
        <tr>
            <td>Total Income</td>
            <td class="amount" style="width: 120px;">{{ number_format($totals['total_income'], 2) }}</td>
        </tr>
        <tr>
            <td>Total Expenses</td>
            <td class="amount">{{ number_format($totals['total_expense'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Net Amount</td>
            <td class="amount">{{ number_format($totals['net_amount'], 2) }}</td>
        </tr>
    </table>

    @include('pdfs.closing-statement.partials.report-footer', ['report_title' => $report_title])
</body>
</html>