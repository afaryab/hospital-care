<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Expense Report - {{ $closing['ct_number'] }}</title>
    @php $report_title = 'Expense Report'; $report_color = '#dc2626'; @endphp
</head>
<body>
    @include('pdfs.closing-statement.partials.report-header', ['report_title' => $report_title, 'report_color' => $report_color])

    {{-- Expense Transactions Table --}}
    @if(count($expenses) > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 42px;">Time</th>
                <th style="width: 56px;">Type</th>
                <th>Category</th>
                <th>Paid To</th>
                <th>Voucher</th>
                <th style="max-width: 100px;">Notes</th>
                <th class="amount" style="width: 75px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $idx => $expense)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($expense['created_at'])->format('H:i') }}</td>
                <td>
                    <span class="badge {{ $expense['income_or_expense'] === 'VOUCHER-PAY' ? 'badge-orange' : 'badge-red' }}">
                        {{ $expense['income_or_expense'] === 'VOUCHER-PAY' ? 'Voucher' : 'Expense' }}
                    </span>
                </td>
                <td>{{ $expense['category_name'] }}</td>
                <td class="text-bold">{{ $expense['paid_to'] ?? '-' }}</td>
                <td><span class="mono">{{ $expense['voucher_number'] ?? '-' }}</span></td>
                <td style="max-width: 100px; overflow: hidden;">{{ \Illuminate\Support\Str::limit($expense['notes'] ?? '-', 40) }}</td>
                <td class="amount">{{ number_format($expense['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Expenses</td>
                <td class="amount">{{ number_format($total_expenses_detail, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No expense transactions recorded for this counter.</div>
    @endif

    {{-- Expense Summary --}}
    <div class="section-title">Counter Balance Summary</div>
    <table>
        <tr>
            <td>Total Income</td>
            <td class="amount" style="width: 120px;">{{ number_format($totals['total_income'], 2) }}</td>
        </tr>
        <tr>
            <td style="color: #dc2626;">Total Expense</td>
            <td class="amount" style="color: #dc2626;">{{ number_format($totals['total_expense'], 2) }}</td>
        </tr>
        <tr class="total-row">
            <td>Net Amount</td>
            <td class="amount">{{ number_format($totals['net_amount'], 2) }}</td>
        </tr>
    </table>

    @include('pdfs.closing-statement.partials.report-footer', ['report_title' => $report_title])
</body>
</html>
