<div class="space-y-4">
    @if($receivables->isEmpty())
        <div class="text-center py-8 text-gray-500">No pending receivables for this panel.</div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3 text-right">Amount (PKR)</th>
                        <th class="px-4 py-3 text-right">Original (PKR)</th>
                        <th class="px-4 py-3">Due Date</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Created</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($receivables as $receivable)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-mono text-gray-500">{{ $receivable->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $receivable->patient?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold">{{ number_format($receivable->amount, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono text-gray-500">{{ $receivable->orignal_amount ? number_format($receivable->orignal_amount, 2) : '—' }}</td>
                            <td class="px-4 py-3">{{ $receivable->due_date ? \Carbon\Carbon::parse($receivable->due_date)->format('d M Y') : '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                    {{ in_array($receivable->status, ['unpaid', 'PENDING']) ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700' }}">
                                    {{ ucfirst(strtolower($receivable->status)) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-500">{{ $receivable->created_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $receivables->links() }}</div>
    @endif
</div>
