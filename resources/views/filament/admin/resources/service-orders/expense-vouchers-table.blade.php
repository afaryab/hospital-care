@php $vouchers = $getState() ?? collect(); @endphp

@if($vouchers->isEmpty())
    <p class="text-sm text-gray-500 italic">No expense vouchers found.</p>
@else
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-white/5 dark:text-gray-400">
            <tr>
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">Voucher#</th>
                <th class="px-3 py-2">Category</th>
                <th class="px-3 py-2">Paid To</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vouchers as $i => $voucher)
            <tr class="border-b dark:border-gray-700">
                <td class="px-3 py-2">{{ $i + 1 }}</td>
                <td class="px-3 py-2">{{ $voucher->created_at->format('d M Y') }}</td>
                <td class="px-3 py-2 font-mono text-xs">{{ $voucher->vc_number ?? '-' }}</td>
                <td class="px-3 py-2">{{ $voucher->expCategory?->name ?? '-' }}</td>
                <td class="px-3 py-2">{{ $voucher->payedTo?->name ?? '-' }}</td>
                <td class="px-3 py-2">
                    @php
                        $status = $voucher->status;
                        $colors = match(strtolower($status)) {
                            'payed', 'paid' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colors }}">{{ strtoupper($status) }}</span>
                </td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($voucher->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            @php
                $paidTotal = $vouchers->filter(fn($v) => strtolower($v->status) === 'payed' || strtolower($v->status) === 'paid')->sum('amount');
                $pendingTotal = $vouchers->filter(fn($v) => strtolower($v->status) === 'pending')->sum('amount');
            @endphp
            <tr class="font-bold bg-gray-50 dark:bg-white/5">
                <td colspan="6" class="px-3 py-2 text-right">Paid Total</td>
                <td class="px-3 py-2 text-right font-mono text-green-700 dark:text-green-400">{{ number_format($paidTotal, 2) }}</td>
            </tr>
            <tr class="font-bold bg-gray-50 dark:bg-white/5">
                <td colspan="6" class="px-3 py-2 text-right">Pending Total</td>
                <td class="px-3 py-2 text-right font-mono text-orange-700 dark:text-orange-400">{{ number_format($pendingTotal, 2) }}</td>
            </tr>
            <tr class="font-bold bg-gray-100 dark:bg-white/10">
                <td colspan="6" class="px-3 py-2 text-right">Grand Total</td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($vouchers->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif
