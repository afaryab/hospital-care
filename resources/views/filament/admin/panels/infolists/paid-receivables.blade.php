<div class="space-y-4">
    @if($receivables->isEmpty())
        <div class="text-center py-8 text-gray-500">No paid receivables for this panel.</div>
    @else
        <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700">
            <table class="w-full text-sm text-left">
                <thead class="bg-gray-50 dark:bg-gray-800 text-xs uppercase text-gray-600 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Patient</th>
                        <th class="px-4 py-3 text-right">Amount (PKR)</th>
                        <th class="px-4 py-3 text-right">Original (PKR)</th>
                        <th class="px-4 py-3">Paid On</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                    @foreach($receivables as $receivable)
                        <tr class="bg-white dark:bg-gray-900 hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-4 py-3 font-mono text-gray-500">{{ $receivable->id }}</td>
                            <td class="px-4 py-3 font-medium">{{ $receivable->patient?->name ?? '—' }}</td>
                            <td class="px-4 py-3 text-right font-mono font-semibold text-green-700">{{ number_format($receivable->amount, 2) }}</td>
                            <td class="px-4 py-3 text-right font-mono text-gray-500">{{ $receivable->orignal_amount ? number_format($receivable->orignal_amount, 2) : '—' }}</td>
                            <td class="px-4 py-3 text-gray-500">{{ $receivable->updated_at->format('d M Y') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-2">{{ $receivables->links() }}</div>
    @endif
</div>
