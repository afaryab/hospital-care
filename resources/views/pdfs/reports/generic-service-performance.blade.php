{{-- Service Performance Report PDF --}}
@include('pdfs.reports.partials.generic-header', [
    'report_title' => 'Service Performance Report',
    'report_color' => '#dc2626',
    'from' => $from,
    'until' => $until,
    'generated_at' => $generated_at,
])

{{-- Department Summary --}}
@if($by_department->isNotEmpty())
<div class="section-title">Summary by Department</div>
<table>
    <thead>
        <tr>
            <th>Department</th>
            <th class="amount">Orders</th>
            <th class="amount">Income</th>
            <th class="amount">Expenses</th>
            <th class="amount">Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($by_department as $dept => $data)
        <tr class="subtotal-row">
            <td><strong>{{ $dept }}</strong></td>
            <td class="amount">{{ $data['count'] }}</td>
            <td class="amount" style="color: #065f46;">{{ number_format($data['income'], 2) }}</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($data['expense'], 2) }}</td>
            <td class="amount text-bold">{{ number_format($data['income'] - $data['expense'], 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td class="text-right"><strong>Grand Total</strong></td>
            <td class="amount">{{ $orders->count() }}</td>
            <td class="amount" style="color: #065f46;">{{ number_format($total_income, 2) }}</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($total_expense, 2) }}</td>
            <td class="amount text-bold">{{ number_format($total_income - $total_expense, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

{{-- Detail Table --}}
@if($orders->isEmpty())
    <div class="empty-state">No service orders found for the selected period.</div>
@else
<div class="section-title">Service Order Details</div>
<table>
    <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th style="width: 9%;">Date</th>
            <th>Service Order</th>
            <th style="width: 7%;">Dept</th>
            <th style="width: 8%;">Status</th>
            <th class="amount" style="width: 10%;">Income</th>
            <th class="amount" style="width: 10%;">Expenses</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $i => $order)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>@hdate($order->created_at, 'd M Y')</td>
            <td>
                <strong class="mono" style="font-size: 9px;">{{ $order->so_number }}{{ $order->so_short ? ' ('.$order->so_short.')' : '' }}</strong>@if($order->token_short) <span class="badge badge-blue" style="font-size: 8px;">Token #{{ $order->token_short }}</span>@endif<br>
                <span class="text-muted" style="font-size: 9px;">
                    {{ collect([$order->patient?->name, $order->service?->name, $order->doctor?->name])->filter()->implode(' · ') }}
                </span>
            </td>
            <td><span class="badge badge-blue">{{ $order->type }}</span></td>
            <td>
                @php
                    $badgeClass = match($order->status) {
                        'closed' => 'badge-green',
                        'refunded' => 'badge-red',
                        'cancelled' => 'badge-red',
                        default => 'badge-orange',
                    };
                @endphp
                <span class="badge {{ $badgeClass }}">{{ strtoupper($order->status) }}</span>
            </td>
            <td class="amount" style="color: #065f46;">{{ number_format($order->income_total ?? 0, 2) }}</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($order->voucher_total ?? 0, 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="5" class="text-right">Grand Total</td>
            <td class="amount" style="color: #065f46;">{{ number_format($total_income, 2) }}</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($total_expense, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

@include('pdfs.reports.partials.generic-footer', [
    'report_title' => 'Service Performance Report',
    'generated_at' => $generated_at,
])
