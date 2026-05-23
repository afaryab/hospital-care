{{-- Single Service Order Detail PDF --}}
@include('pdfs.reports.partials.generic-header', [
    'report_title' => 'Service Order Detail',
    'report_color' => '#7c3aed',
    'from' => $order->created_at,
    'until' => $order->closed_at ? \Carbon\Carbon::parse($order->closed_at) : now(),
    'generated_at' => $generated_at,
])

{{-- Service Order Info --}}
<div class="section-title">Service Order Information</div>
<table>
    <tr>
        <td style="width: 25%;"><span class="info-label">SO Number</span></td>
        <td class="mono text-bold">{{ $order->so_number }}@if($order->token_short) <span class="badge badge-blue">Token #{{ $order->token_short }}</span>@endif</td>
        <td style="width: 25%;"><span class="info-label">Status</span></td>
        <td>
            <span class="badge {{ $order->status === 'closed' ? 'badge-green' : 'badge-orange' }}">
                {{ strtoupper($order->status) }}
            </span>
        </td>
    </tr>
    <tr>
        <td><span class="info-label">Patient</span></td>
        <td>{{ $order->patient?->name ?? '-' }}</td>
        <td><span class="info-label">Service</span></td>
        <td>{{ $order->service?->name ?? '-' }}</td>
    </tr>
    <tr>
        <td><span class="info-label">Provider</span></td>
        <td>{{ $order->doctor?->name ?? '-' }}</td>
        <td><span class="info-label">Created</span></td>
        <td>@hdate($order->created_at, 'd M Y, H:i')</td>
    </tr>
    @if($order->closed_at)
    <tr>
        <td><span class="info-label">Closed At</span></td>
        <td>@hdate($order->closed_at, 'd M Y, H:i')</td>
        <td colspan="2"></td>
    </tr>
    @endif
</table>

{{-- Transaction Elements (Income) --}}
<div class="section-title">Transactions (Income)</div>
@if($incomeElements->isEmpty())
    <div class="empty-state">No income transactions found.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 14%;">Date</th>
                <th>TR#</th>
                <th>Patient</th>
                <th>Type</th>
                <th>Payment</th>
                <th class="amount" style="width: 12%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomeElements as $i => $el)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>@hdate($el->created_at, 'd M Y')</td>
                <td class="mono">{{ $el->transaction?->tr_number ?? '-' }}</td>
                <td>{{ $el->patient?->name ?? '-' }}</td>
                <td><span class="badge badge-blue">{{ $el->type }}</span></td>
                <td>{{ $el->transaction?->type ?? '-' }}</td>
                <td class="amount">{{ number_format($el->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Income</td>
                <td class="amount">{{ number_format($incomeElements->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- Receivables --}}
<div class="section-title">Receivables</div>
@if($receivables->isEmpty())
    <div class="empty-state">No receivables found for this service order.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 14%;">Date</th>
                <th>TR#</th>
                <th>Patient</th>
                <th>Panel</th>
                <th>Due Date</th>
                <th>Status</th>
                <th class="amount" style="width: 12%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receivables as $i => $rec)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>@hdate($rec->created_at, 'd M Y')</td>
                <td class="mono">{{ $rec->transaction?->tr_number ?? '-' }}</td>
                <td>{{ $rec->patient?->name ?? '-' }}</td>
                <td>{{ $rec->panel?->name ?? '-' }}</td>
                <td>{{ $rec->due_date ? \App\Helpers\DateHelper::pdfFormat($rec->due_date, 'd M Y') : '-' }}</td>
                <td>
                    @php
                        $statusBadge = match(strtolower($rec->status)) {
                            'paid', 'payed' => 'badge-green',
                            'pending', 'unpaid' => 'badge-orange',
                            default => 'badge-blue',
                        };
                    @endphp
                    <span class="badge {{ $statusBadge }}">{{ strtoupper($rec->status) }}</span>
                </td>
                <td class="amount">{{ number_format($rec->orignal_amount ?? $rec->amount, 2) }}</td>
                <td class="amount">{{ number_format($rec->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="7" class="text-right">Total Receivables</td>
                <td class="amount">{{ number_format($receivables->sum(fn($r) => $r->orignal_amount ?? $r->amount), 2) }}</td>
                <td class="amount">{{ number_format($receivables->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- Receivable Payments --}}
@if($receivablePayments->isNotEmpty())
<div class="section-title">Receivable Payments</div>
<table>
    <thead>
        <tr>
            <th style="width: 4%;">#</th>
            <th style="width: 14%;">Date</th>
            <th>TR#</th>
            <th>Receivable</th>
            <th>Payment Type</th>
            <th class="amount" style="width: 12%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receivablePayments as $i => $tx)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>@hdate($tx->created_at, 'd M Y')</td>
            <td class="mono">{{ $tx->tr_number ?? '-' }}</td>
            <td class="mono">{{ $tx->receaveable?->transaction?->tr_number ?? '-' }}</td>
            <td>{{ $tx->type ?? '-' }}</td>
            <td class="amount">{{ number_format($tx->amount, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="5" class="text-right">Total Payments</td>
            <td class="amount">{{ number_format($receivablePayments->sum('amount'), 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

{{-- Expense Vouchers --}}
<div class="section-title">Expense Vouchers</div>
@if($expenseVouchers->isEmpty())
    <div class="empty-state">No expense vouchers issued for this service order.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="width: 4%;">#</th>
                <th style="width: 14%;">Date</th>
                <th>Voucher#</th>
                <th>Category</th>
                <th>Paid To</th>
                <th>Status</th>
                <th class="amount" style="width: 12%;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenseVouchers as $i => $vc)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>@hdate($vc->created_at, 'd M Y')</td>
                <td class="mono">{{ $vc->vc_number ?? '-' }}</td>
                <td>{{ $vc->expCategory?->name ?? '-' }}</td>
                <td>{{ $vc->payedTo?->name ?? $vc->payed_to_name ?? '-' }}</td>
                <td>
                    <span class="badge {{ $vc->status === 'payed' ? 'badge-green' : 'badge-orange' }}">
                        {{ strtoupper($vc->status) }}
                    </span>
                </td>
                <td class="amount">{{ number_format($vc->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="6" class="text-right">Total Vouchers</td>
                <td class="amount">{{ number_format($expenseVouchers->sum('amount'), 2) }}</td>
            </tr>
            <tr class="subtotal-row">
                <td colspan="6" class="text-right">Paid</td>
                <td class="amount" style="color: #065f46;">{{ number_format($expenseVouchers->where('status', 'payed')->sum('amount'), 2) }}</td>
            </tr>
            <tr class="subtotal-row">
                <td colspan="6" class="text-right">Pending</td>
                <td class="amount" style="color: #991b1b;">{{ number_format($expenseVouchers->where('status', '!=', 'payed')->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- Grand Summary --}}
<div class="section-title">Financial Summary</div>
<table>
    <tr>
        <td style="width: 60%;">Total Income (Transactions)</td>
        <td class="amount text-bold" style="color: #065f46;">{{ number_format($incomeElements->sum('amount'), 2) }}</td>
    </tr>
    <tr>
        <td>Total Receivables</td>
        <td class="amount">{{ number_format($receivables->sum('amount'), 2) }}</td>
    </tr>
    <tr>
        <td>Total Receivable Payments Collected</td>
        <td class="amount">{{ number_format($receivablePayments->sum('amount'), 2) }}</td>
    </tr>
    <tr>
        <td>Total Expense Vouchers Issued</td>
        <td class="amount text-bold" style="color: #991b1b;">{{ number_format($expenseVouchers->sum('amount'), 2) }}</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;↳ Paid</td>
        <td class="amount" style="color: #065f46;">{{ number_format($expenseVouchers->where('status', 'payed')->sum('amount'), 2) }}</td>
    </tr>
    <tr>
        <td>&nbsp;&nbsp;&nbsp;↳ Pending</td>
        <td class="amount" style="color: #991b1b;">{{ number_format($expenseVouchers->where('status', '!=', 'payed')->sum('amount'), 2) }}</td>
    </tr>
    <tr class="total-row">
        <td>Net (Income − Vouchers Issued)</td>
        <td class="amount">{{ number_format($incomeElements->sum('amount') - $expenseVouchers->sum('amount'), 2) }}</td>
    </tr>
</table>

@include('pdfs.reports.partials.generic-footer', [
    'report_title' => 'Service Order Detail — ' . $order->so_number,
    'generated_at' => $generated_at,
])
