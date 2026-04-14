<div class="space-y-4">
    @if($cheques->isEmpty())
        <div class="text-center py-8 text-gray-500">No cheques recorded for this panel.</div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">Cheque #</th>
                        <th class="px-4 py-3 text-right">Amount (PKR)</th>
                        <th class="px-4 py-3">Bank Account</th>
                        <th class="px-4 py-3">Cheque Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Received At</th>
                        <th class="px-4 py-3">Notes</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($cheques as $cheque)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-mono font-semibold">{{ $cheque->cheque_number }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold">{{ number_format($cheque->amount, 2) }}</td>
                            <td class="px-4 py-3">{{ $cheque->bankAccount?->name ?? '—' }}</td>
                            <td class="px-4 py-3">{{ $cheque->due_date ? $cheque->due_date->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    @if($cheque->status === 'received') bg-green-100 text-green-700
                                    @elseif($cheque->status === 'bounced') bg-red-100 text-red-700
                                    @else bg-yellow-100 text-yellow-700
                                    @endif">
                                    {{ ucfirst($cheque->status) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $cheque->received_at ? $cheque->received_at->format('d M Y H:i') : '—' }}</td>
                            <td class="px-4 py-3 text-gray-500 max-w-xs truncate">{{ $cheque->notes ?? '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $cheques->links() }}</div>
    @endif
</div>
