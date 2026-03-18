@php $receivables = $getState() ?? collect(); @endphp

@if($receivables->isEmpty())
    <p class="text-sm text-gray-500 italic">No receivables found.</p>
@else
<div class="overflow-x-auto">
    <table class="w-full text-sm text-left">
        <thead class="text-xs text-gray-700 uppercase bg-gray-50 dark:bg-white/5 dark:text-gray-400">
            <tr>
                <th class="px-3 py-2">#</th>
                <th class="px-3 py-2">Date</th>
                <th class="px-3 py-2">TR#</th>
                <th class="px-3 py-2">Patient</th>
                <th class="px-3 py-2">Panel</th>
                <th class="px-3 py-2">Due Date</th>
                <th class="px-3 py-2">Status</th>
                <th class="px-3 py-2 text-right">Orignal</th>
                <th class="px-3 py-2 text-right">Remaining</th>
            </tr>
        </thead>
        <tbody>
            @foreach($receivables as $i => $rec)
            <tr class="border-b dark:border-gray-700">
                <td class="px-3 py-2">{{ $i + 1 }}</td>
                <td class="px-3 py-2">{{ $rec->created_at->format('d M Y') }}</td>
                <td class="px-3 py-2 font-mono text-xs">{{ $rec->transaction?->tr_number ?? '-' }}</td>
                <td class="px-3 py-2">{{ $rec->patient?->name ?? '-' }}</td>
                <td class="px-3 py-2">{{ $rec->panel?->name ?? '-' }}</td>
                <td class="px-3 py-2">{{ $rec->due_date ? \Carbon\Carbon::parse($rec->due_date)->format('d M Y') : '-' }}</td>
                <td class="px-3 py-2">
                    @php
                        $colors = match(strtolower($rec->status)) {
                            'paid', 'payed' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                            'unpaid', 'pending' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200',
                            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium {{ $colors }}">{{ strtoupper($rec->status) }}</span>
                </td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($rec->orignal_amount ?? $rec->amount, 2) }}</td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($rec->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="font-bold bg-gray-50 dark:bg-white/5">
                <td colspan="7" class="px-3 py-2 text-right">Total</td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($receivables->sum(fn($r) => $r->orignal_amount ?? $r->amount), 2) }}</td>
                <td class="px-3 py-2 text-right font-mono">{{ number_format($receivables->sum('amount'), 2) }}</td>
            </tr>
        </tfoot>
    </table>
</div>
@endif
