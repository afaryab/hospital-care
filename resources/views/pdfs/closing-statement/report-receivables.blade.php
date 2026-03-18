<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receivables Report - {{ $closing['ct_number'] }}</title>
    @php $report_title = 'Receivables Report'; $report_color = '#7c3aed'; @endphp
</head>
<body>
    @include('pdfs.closing-statement.partials.report-header', ['report_title' => $report_title, 'report_color' => $report_color])

    {{-- Receivables Table --}}
    @if(count($receivables) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th>Transaction</th>
                <th>Patient</th>
                <th>Panel</th>
                <th style="width: 70px;">Due Date</th>
                <th style="width: 56px;">Status</th>
                <th class="amount" style="width: 75px;">Orignal</th>
                <th class="amount" style="width: 75px;">Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receivables as $idx => $rec)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td><span class="mono">{{ $rec['transaction_number'] }}</span></td>
                <td class="text-bold">{{ $rec['patient_name'] }}</td>
                <td>{{ $rec['panel_name'] }}</td>
                <td>{{ $rec['due_date'] ? \Carbon\Carbon::parse($rec['due_date'])->format('d/m/Y') : '-' }}</td>
                <td>
                    @php
                        $statusBadge = match(strtoupper($rec['status'])) {
                            'PAID' => 'badge-green',
                            'PARTIAL' => 'badge-amber',
                            default => 'badge-orange',
                        };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ $rec['status'] }}</span>
                </td>
                <td class="amount">{{ number_format($rec['orignal_amount'] ?? $rec['amount'], 2) }}</td>
                <td class="amount">{{ number_format($rec['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Receivables ({{ count($receivables) }} items)</td>
                <td class="amount">{{ number_format(collect($receivables)->sum('orignal_amount'), 2) }}</td>
                <td class="amount">{{ number_format($total_receivables, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No receivables found for this counter.</div>
    @endif

    {{-- Receivables vs Income Summary --}}
    <div class="section-title">Receivables Summary</div>
    <table>
        <tr>
            <td>Total Income</td>
            <td class="amount" style="width: 120px;">{{ number_format($totals['total_income'], 2) }}</td>
        </tr>
        <tr>
            <td style="color: #7c3aed;">Outstanding Receivables</td>
            <td class="amount" style="color: #7c3aed;">{{ number_format($total_receivables, 2) }}</td>
        </tr>
        <tr>
            <td>Number of Receivables</td>
            <td class="amount">{{ count($receivables) }}</td>
        </tr>
    </table>

    @include('pdfs.closing-statement.partials.report-footer', ['report_title' => $report_title])
</body>
</html>
