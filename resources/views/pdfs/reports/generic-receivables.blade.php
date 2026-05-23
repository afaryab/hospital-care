<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Receivables Report</title>
    @php $report_title = 'Receivables Report'; $report_color = '#7c3aed'; @endphp
</head>
<body>
    @include('pdfs.reports.partials.generic-header')

    @if($items->count() > 0)
    <table>
        <thead>
            <tr>
                <th style="width: 28px;">#</th>
                <th style="width: 60px;">Date</th>
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
            @foreach($items as $idx => $rec)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td>@hdate($rec->created_at, 'd/m/Y')</td>
                <td><span class="mono">{{ $rec->transaction?->tr_number ?? '-' }}</span></td>
                <td class="text-bold">{{ $rec->patient?->name ?? 'N/A' }}</td>
                <td>{{ $rec->panel?->name ?? 'N/A' }}</td>
                <td>{{ $rec->due_date ? \App\Helpers\DateHelper::pdfFormat($rec->due_date, 'd/m/Y') : '-' }}</td>
                <td>
                    @php
                        $statusBadge = match(strtoupper($rec->status ?? '')) {
                            'PAID', 'PAYED' => 'badge-green',
                            'PARTIAL' => 'badge-amber',
                            default => 'badge-orange',
                        };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ $rec->status ?? 'PENDING' }}</span>
                </td>
                <td class="amount">{{ number_format($rec->orignal_amount ?? $rec->amount, 2) }}</td>
                <td class="amount">{{ number_format($rec->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Receivables ({{ $items->count() }} items)</td>
                <td class="amount">{{ number_format($items->sum(fn($r) => $r->orignal_amount ?? $r->amount), 2) }}</td>
                <td class="amount">{{ number_format($total, 2) }}</td>
            </tr>
        </tfoot>
    </table>
    @else
        <div class="empty-state">No receivables found for this period.</div>
    @endif

    @include('pdfs.reports.partials.generic-footer')
</body>
</html>
