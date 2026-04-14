<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Pending Panel Payments</title>
    @php $report_title = 'Pending Panel Payments'; $report_color = '#7c3aed'; @endphp
</head>
<body>
    @include('pdfs.reports.partials.generic-header')

    @if($cheques->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 72px;">Date</th>
                <th>Panel</th>
                <th style="width: 90px;">Cheque No.</th>
                <th>Bank Account</th>
                <th style="width: 72px;">Due Date</th>
                <th class="amount" style="width: 80px;">Amount (PKR)</th>
                <th>Notes</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cheques as $idx => $cheque)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>{{ $cheque->created_at->format('d/m/Y') }}</td>
                <td class="text-bold">{{ $cheque->panel?->name ?? '-' }}</td>
                <td><span class="mono">{{ $cheque->cheque_number ?? '-' }}</span></td>
                <td>{{ $cheque->bankAccount?->name ?? '-' }}</td>
                <td>{{ $cheque->due_date ? \Carbon\Carbon::parse($cheque->due_date)->format('d/m/Y') : '-' }}</td>
                <td class="amount">{{ number_format((float) $cheque->amount, 2) }}</td>
                <td class="text-muted">{{ $cheque->notes ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Pending ({{ $cheques->count() }} cheques)</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No pending panel payments found for this period.</div>
    @endif

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
