<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Income Report</title>
    @php $report_title = 'Income Report'; $report_color = '#059669'; @endphp
</head>
<body>
    @include('pdfs.reports.partials.generic-header')

    @if($elements->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 60px;">Date</th>
                <th>Counter</th>
                <th>Patient</th>
                <th>Service</th>
                <th>Provider</th>
                <th style="width: 50px;">Method</th>
                <th class="amount" style="width: 75px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($elements as $idx => $el)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>@hdate($el->created_at, 'd/m H:i')</td>
                <td><span class="mono">{{ $el->transaction?->closing?->ct_number ?? '-' }}</span></td>
                <td>{{ $el->patient?->name ?? '-' }}</td>
                <td>{{ $el->service?->name ?? '-' }}</td>
                <td>{{ $el->doctor?->name ?? '-' }}</td>
                <td>
                    @php
                        $type = $el->transaction?->type ?? $el->type;
                        $badgeClass = match($type) {
                            'CASH' => 'badge-green', 'CARD' => 'badge-blue',
                            'CHEQUE' => 'badge-amber', 'PANEL' => 'badge-purple',
                            default => 'badge-green',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $type }}</span>
                </td>
                <td class="amount">{{ number_format($el->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Income ({{ $elements->count() }} items)</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No income transactions found for this period.</div>
    @endif

    @if($by_type->count() > 0)
    <div class="section-title">Income by Payment Method</div>
    <table>
        <thead>
            <tr>
                <th>Payment Method</th>
                <th class="amount" style="width: 100px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($by_type as $type => $amount)
            <tr>
                <td>
                    @php
                        $badgeClass = match($type) {
                            'CASH' => 'badge-green', 'CARD' => 'badge-blue',
                            'CHEQUE' => 'badge-amber', 'PANEL' => 'badge-purple',
                            default => 'badge-green',
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }}">{{ $type }}</span>
                </td>
                <td class="amount">{{ number_format($amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td class="text-right">Grand Total</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @endif

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
