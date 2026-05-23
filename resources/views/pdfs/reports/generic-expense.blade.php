<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Expense Report</title>
    @php $report_title = 'Expense Report'; $report_color = '#dc2626'; @endphp
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
                <th style="width: 56px;">Type</th>
                <th>Category</th>
                <th>Paid To</th>
                <th>Voucher</th>
                <th class="amount" style="width: 75px;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($elements as $idx => $el)
            @php
                $categoryName = $el->expenseCategory?->name ?? 'N/A';
                if ($el->type === 'VOUCHER_PAY' && $el->expVoucher?->expCategory) {
                    $categoryName = $el->expVoucher->expCategory->name;
                }
            @endphp
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>@hdate($el->created_at, 'd/m H:i')</td>
                <td><span class="mono">{{ $el->transaction?->closing?->ct_number ?? '-' }}</span></td>
                <td>
                    <span class="badge {{ $el->type === 'VOUCHER_PAY' ? 'badge-orange' : 'badge-red' }}">
                        {{ $el->type === 'VOUCHER_PAY' ? 'Voucher' : $el->type }}
                    </span>
                </td>
                <td>{{ $categoryName }}</td>
                <td class="text-bold">{{ $el->expVoucher?->payedTo?->name ?? '-' }}</td>
                <td><span class="mono">{{ $el->expVoucher?->vc_number ?? '-' }}</span></td>
                <td class="amount">{{ number_format($el->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Expenses ({{ $elements->count() }} items)</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No expense transactions found for this period.</div>
    @endif

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
