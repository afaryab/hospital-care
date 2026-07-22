{{-- Service Order Report PDF (list view) --}}
@include('pdfs.reports.partials.generic-header', [
    'report_title' => 'Service Order Report',
    'report_color' => '#7c3aed',
    'from' => $from,
    'until' => $until,
    'generated_at' => $generated_at,
])

@if($orders->isEmpty())
    <div class="empty-state">No service orders found for the selected period.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="width: 3%;">#</th>
                <th style="width: 10%;">Date</th>
                <th>Service Order</th>
                <th style="width: 7%;">Status</th>
                <th class="amount" style="width: 10%;">Income</th>
                <th class="amount" style="width: 10%;">Vouchers</th>
                <th class="amount" style="width: 10%;">Paid</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $i => $order)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>@hdate($order->created_at, 'd M Y')</td>
                <td>
                    <strong class="mono">{{ $order->so_number }}</strong>@if($order->token_short) <span class="badge badge-blue" style="font-size: 8px;">Token #{{ $order->token_short }}</span>@endif<br>
                    <span class="text-muted" style="font-size: 9px;">{{ collect([$order->patient?->name, $order->service?->name, $order->doctor?->name])->filter()->implode(' · ') }}</span>
                </td>
                <td>
                    <span class="badge {{ $order->status === 'closed' ? 'badge-green' : 'badge-orange' }}">
                        {{ strtoupper($order->status) }}
                    </span>
                </td>
                <td class="amount">{{ number_format($order->income_total ?? 0, 2) }}</td>
                <td class="amount">{{ number_format($order->voucher_total ?? 0, 2) }}</td>
                <td class="amount">{{ number_format($order->paid_total ?? 0, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right">Grand Total</td>
                <td class="amount">{{ number_format($orders->sum('income_total'), 2) }}</td>
                <td class="amount">{{ number_format($orders->sum('voucher_total'), 2) }}</td>
                <td class="amount">{{ number_format($orders->sum('paid_total'), 2) }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- Summary --}}
    <div class="section-title">Summary</div>
    <table>
        <tr>
            <td>Total Service Orders</td>
            <td class="amount text-bold">{{ $orders->count() }}</td>
        </tr>
        <tr>
            <td>Open Orders</td>
            <td class="amount">{{ $orders->where('status', 'open')->count() }}</td>
        </tr>
        <tr>
            <td>Closed Orders</td>
            <td class="amount">{{ $orders->where('status', 'closed')->count() }}</td>
        </tr>
        <tr>
            <td>Total Income</td>
            <td class="amount text-bold" style="color: #065f46;">{{ number_format($orders->sum('income_total'), 2) }}</td>
        </tr>
        <tr>
            <td>Total Voucher Amount</td>
            <td class="amount text-bold" style="color: #991b1b;">{{ number_format($orders->sum('voucher_total'), 2) }}</td>
        </tr>
        <tr>
            <td>Total Paid (from Vouchers)</td>
            <td class="amount text-bold" style="color: #1e40af;">{{ number_format($orders->sum('paid_total'), 2) }}</td>
        </tr>
    </table>
@endif

@include('pdfs.reports.partials.generic-footer', [
    'report_title' => 'Service Order Report',
    'generated_at' => $generated_at,
])
