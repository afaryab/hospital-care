<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pending Bank Payments</title>
    @php $report_title = 'Pending Bank Payments'; $report_color = '#2563eb'; @endphp
</head>
<body>
    @include('pdfs.reports.partials.generic-header')

    @if($transactions->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 72px;">Date</th>
                <th>Bank Account</th>
                <th>Description</th>
                <th style="width: 120px;">Reference No.</th>
                <th class="amount" style="width: 80px;">Credit (PKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $idx => $txn)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($txn->transaction_date)->format('d/m/Y') }}</td>
                <td class="text-bold">{{ $txn->bankAccount?->name ?? '-' }}</td>
                <td class="text-muted">{{ $txn->description ?? '-' }}</td>
                <td><span class="mono">{{ $txn->reference_number ?? '-' }}</span></td>
                <td class="amount">{{ number_format((float) ($txn->credit ?? 0), 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="5" class="text-right">Total Unlinked Credits ({{ $transactions->count() }} items)</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No pending bank payments found for this period.</div>
    @endif

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
