<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Received Bank Payments</title>
    @php $report_title = 'Received Bank Payments'; $report_color = '#059669'; @endphp
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
                <th style="width: 110px;">Reference No.</th>
                <th class="amount" style="width: 80px;">Credit (PKR)</th>
                <th style="width: 90px;">TR Number</th>
                <th>Patient</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $idx => $txn)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ \App\Helpers\DateHelper::pdfFormat($txn->transaction_date, 'd/m/Y') }}</td>
                <td class="text-bold">{{ $txn->bankAccount?->name ?? '-' }}</td>
                <td><span class="mono">{{ $txn->reference_number ?? '-' }}</span></td>
                <td class="amount">{{ number_format((float) ($txn->credit ?? 0), 2) }}</td>
                <td><span class="mono">{{ $txn->linkedTransaction?->tr_number ?? '-' }}</span></td>
                <td>{{ $txn->linkedTransaction?->patient?->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Total Received ({{ $transactions->count() }} items)</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
                <td colspan="2"></td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No received bank payments found for this period.</div>
    @endif

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
