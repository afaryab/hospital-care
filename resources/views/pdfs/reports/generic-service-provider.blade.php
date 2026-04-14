{{-- Service Provider Report PDF --}}
@include('pdfs.reports.partials.generic-header', [
    'report_title' => 'Service Provider Report' . ($provider ? ': ' . $provider->name : ''),
    'report_color' => '#4f46e5',
    'from' => $from,
    'until' => $until,
    'generated_at' => $generated_at,
])

{{-- Provider Info --}}
@if($provider)
<div class="section-title">Provider</div>
<table>
    <tr>
        <td style="width: 30%;"><strong>Name</strong></td>
        <td>{{ $provider->name }}</td>
    </tr>
    <tr>
        <td><strong>Period</strong></td>
        <td>{{ $from->format('d M Y') }} – {{ $until->format('d M Y') }}</td>
    </tr>
    <tr>
        <td><strong>Total Income Generated</strong></td>
        <td class="text-bold" style="color: #065f46;">{{ number_format($total_income, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Total Expenses Paid to Provider</strong></td>
        <td class="text-bold" style="color: #991b1b;">{{ number_format($total_expense, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Net</strong></td>
        <td class="text-bold">{{ number_format($total_income - $total_expense, 2) }}</td>
    </tr>
</table>
@else
<div class="sub-section">All providers included — select a specific provider to filter.</div>
<table>
    <tr>
        <td><strong>Total Income Generated</strong></td>
        <td class="text-bold" style="color: #065f46;">{{ number_format($total_income, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Total Expenses Paid to Providers</strong></td>
        <td class="text-bold" style="color: #991b1b;">{{ number_format($total_expense, 2) }}</td>
    </tr>
    <tr>
        <td><strong>Net</strong></td>
        <td class="text-bold">{{ number_format($total_income - $total_expense, 2) }}</td>
    </tr>
</table>
@endif

{{-- Service Orders Detail --}}
@if($orders->isEmpty())
    <div class="empty-state">No service orders found for the selected period and filters.</div>
@else
<div class="section-title">Service Orders</div>
<table>
    <thead>
        <tr>
            <th style="width: 3%;">#</th>
            <th style="width: 9%;">Date</th>
            <th>Service Order</th>
            <th style="width: 7%;">Dept</th>
            <th style="width: 8%;">Status</th>
            <th class="amount" style="width: 11%;">Income</th>
            <th class="amount" style="width: 11%;">Paid to Provider</th>
            <th class="amount" style="width: 9%;">Net</th>
        </tr>
    </thead>
    <tbody>
        @foreach($orders as $i => $order)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td>{{ $order->created_at->format('d M Y') }}</td>
            <td>
                <strong class="mono" style="font-size: 9px;">{{ $order->so_number }}</strong><br>
                <span class="text-muted" style="font-size: 9px;">
                    {{ collect([$order->patient?->name, $order->service?->name])->filter()->implode(' · ') }}
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
            <td class="amount text-bold">{{ number_format(($order->income_total ?? 0) - ($order->voucher_total ?? 0), 2) }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="5" class="text-right">Grand Total</td>
            <td class="amount" style="color: #065f46;">{{ number_format($total_income, 2) }}</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($total_expense, 2) }}</td>
            <td class="amount text-bold">{{ number_format($total_income - $total_expense, 2) }}</td>
        </tr>
    </tfoot>
</table>

{{-- Expense Vouchers breakdown per service order --}}
@php $ordersWithVouchers = $orders->filter(fn ($o) => $o->expenseVouchers->isNotEmpty()); @endphp
@if($ordersWithVouchers->isNotEmpty())
<div class="section-title">Expense Vouchers Paid to Provider</div>
<table>
    <thead>
        <tr>
            <th style="width: 10%;">Date</th>
            <th style="width: 18%;">VC Number</th>
            <th>Service Order</th>
            <th>Category</th>
            <th class="amount" style="width: 11%;">Amount</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ordersWithVouchers as $order)
            @foreach($order->expenseVouchers as $voucher)
            <tr>
                <td>{{ $voucher->created_at->format('d M Y') }}</td>
                <td class="mono">{{ $voucher->vc_number }}</td>
                <td class="mono" style="font-size: 9px;">{{ $order->so_number }}</td>
                <td>{{ $voucher->expCategory?->name ?? '-' }}</td>
                <td class="amount" style="color: #991b1b;">{{ number_format($voucher->amount, 2) }}</td>
            </tr>
            @endforeach
        @endforeach
    </tbody>
    <tfoot>
        <tr class="total-row">
            <td colspan="4" class="text-right">Total Paid</td>
            <td class="amount" style="color: #991b1b;">{{ number_format($total_expense, 2) }}</td>
        </tr>
    </tfoot>
</table>
@endif

@endif

@include('pdfs.reports.partials.generic-footer', [
    'report_title' => 'Service Provider Report',
    'generated_at' => $generated_at,
])
