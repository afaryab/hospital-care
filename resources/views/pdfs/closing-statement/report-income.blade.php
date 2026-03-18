<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Income Report - {{ $closing['ct_number'] }}</title>
    @php $report_title = 'Income Report'; $report_color = '#059669'; @endphp
</head>
<body>
    @include('pdfs.closing-statement.partials.report-header', ['report_title' => $report_title, 'report_color' => $report_color])

    {{-- Income Transactions Table --}}
    @if(count($transactions['income']) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 42px;">Time</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Doctor</th>
                <th style="width: 50px;">Method</th>
                <th class="amount" style="width: 75px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @php $counter = 0; @endphp
            @foreach($transactions['income'] as $transaction)
                @foreach($transaction['elements'] as $element)
                    @php $counter++; @endphp
                    <tr @if($transaction['has_receaveable']) style="background: #faf5ff;" @endif>
                        <td>{{ $counter }}</td>
                        <td>{{ $element['created_at']->format('H:i') }}</td>
                        <td>
                            {{ $element['patient_name'] ?? '-' }}
                            @if($element['patient_ps_number'])
                                <br><span class="text-muted mono">{{ $element['patient_ps_number'] }}</span>
                            @endif
                        </td>
                        <td>{{ $element['service_name'] }}</td>
                        <td>{{ $element['doctor_name'] ?? '-' }}</td>
                        <td>
                            @php
                                $badgeClass = match($transaction['type']) {
                                    'CASH' => 'badge-green',
                                    'CARD' => 'badge-blue',
                                    'CHEQUE' => 'badge-amber',
                                    'PANEL' => 'badge-purple',
                                    default => 'badge-green',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $transaction['type'] }}</span>
                        </td>
                        <td class="amount">{{ number_format($element['amount'], 2) }}</td>
                    </tr>
                    @if($transaction['has_receaveable'] && $loop->last)
                    <tr style="background: #f3e8ff;">
                        <td colspan="7" style="padding: 3px 8px; border-top: none;">
                            <span class="badge badge-purple">RECEIVABLE</span>
                            <span style="font-size: 10px; color: #6b21a8; margin-left: 4px;">
                                Panel: <strong>{{ $transaction['receaveable_panel'] ?? 'N/A' }}</strong>
                                &bull; Patient: <strong>{{ $transaction['receaveable_patient'] ?? 'N/A' }}</strong>
                                &bull; Amount: <strong>{{ number_format($transaction['receaveable_amount'] ?? 0, 2) }}</strong>
                                &bull; Status: <span class="badge {{ $transaction['receaveable_status'] === 'PAID' ? 'badge-green' : 'badge-orange' }}">{{ $transaction['receaveable_status'] }}</span>
                            </span>
                        </td>
                    </tr>
                    @endif
                @endforeach
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Income</td>
                <td class="amount">{{ number_format($totals['total_income'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No income transactions recorded for this counter.</div>
    @endif

    {{-- Summary by Payment Method --}}
    @if(count($totals['by_type']) > 0)
    <div class="section-title">Income by Payment Method</div>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="amount" style="width: 100px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($totals['by_type'] as $type => $amount)
                @if($amount > 0)
                <tr>
                    <td>
                        @php
                            $badgeClass = match($type) {
                                'CASH' => 'badge-green',
                                'CARD' => 'badge-blue',
                                'CHEQUE' => 'badge-amber',
                                'PANEL' => 'badge-purple',
                                default => 'badge-green',
                            };
                        @endphp
                        <span class="badge {{ $badgeClass }}">{{ $type }}</span>
                    </td>
                    <td class="amount">{{ number_format($amount, 2) }}</td>
                </tr>
                @endif
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-right">Grand Total</td>
                <td class="amount">{{ number_format($totals['total_income'], 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    {{-- Quick Stats --}}
    <div class="section-title">Summary</div>
    <table>
        <tr>
            <td>Total Transactions</td>
            <td class="amount">{{ $summary['income_count'] }}</td>
        </tr>
        <tr>
            <td>Refunded Transactions</td>
            <td class="amount">{{ $totals['refund_count'] }}</td>
        </tr>
        <tr>
            <td>Edited Transactions</td>
            <td class="amount">{{ $totals['edited_count'] }}</td>
        </tr>
    </table>

    @include('pdfs.closing-statement.partials.report-footer', ['report_title' => $report_title])
</body>
</html>
