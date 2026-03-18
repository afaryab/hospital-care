@php $elements = $getState() ?? collect(); @endphp

@if($elements->isEmpty())
    <p class="text-sm text-gray-500 italic">No income transactions found.</p>
@else
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-white/5 dark:text-gray-400">
            <tr>
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">TR#</th>
                <th class="px-3 py-2">Patient</th>
                <th class="px-3 py-2">Type</th>
                <th class="px-3 py-2">Payment</th>
                <th class="px-3 py-2 text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($elements as $i => $el)
            <tr class="border-b dark:border-gray-700">
                <td class="px-3 py-2">{{ $i + 1 }}</td>
                <td class="px-3 py-2">{{ $el->created_at->format('d M Y') }}</td>
                <td class="px-3 py-2 font-mono text-xs">{{ $el->transaction?->tr_number ?? '-' }}</td>
                <td class="px-3 py-2">{{ $el->patient?->name ?? '-' }}</td>
                <td class="px-3 py-2"><span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">{{ $el->type }}</span></td>
                <td class="px-3 py-2">{{ $el->transaction?->type ?? '-' }}</td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($el->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold bg-gray-50 dark:bg-white/5">
                <td colspan="6" class="px-3 py-2 text-right">Total</td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($elements->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif
