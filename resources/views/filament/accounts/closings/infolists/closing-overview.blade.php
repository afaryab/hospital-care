@php
    $formatMoney = static fn (?float $value): string => $value !== null ? number_format($value, 2) : '-';

    $details = [
        'Counter Number' => $closing->ct_number ?? '-',
        'Status' => $closing->status ?? '-',
        'Reception' => optional($closing->reception)->name ?? '-',
        'Receptionist' => optional($closing->receptionist)->name ?? '-',
        'Opening Amount' => $formatMoney($closing->opening_amount),
        'Closing Amount' => $formatMoney($closing->closing_amount),
        'Cash Received' => $formatMoney($closing->closing_amount_cash),
        'Cheque Received' => $formatMoney($closing->closing_amount_cheque),
        'Card Received' => $formatMoney($closing->closing_amount_card),
        'Expenses Paid' => $formatMoney($closing->expense_payed),
        'Amount Reported' => $formatMoney($closing->amount_received),
        'Cash Receiving Time' => optional($closing->cash_recieving_time)?->format('M d, Y g:i A') ?? '-',
        'Closed At' => optional($closing->closed_at)?->format('M d, Y g:i A') ?? '-',
        'Created At' => optional($closing->created_at)?->format('M d, Y g:i A') ?? '-',
        'Updated At' => optional($closing->updated_at)?->format('M d, Y g:i A') ?? '-',
    ];
@endphp

<div class="grid grid-cols-1 gap-6 lg:grid-cols-3 w-full">
    <div class="space-y-4 lg:col-span-1">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Closing Details</h3>
        <dl class="space-y-3">
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Counter Number
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{$closing->ct_number}}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Status
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{$closing->status}}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Reception
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{$closing->reception?->name}}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Receptionist
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{$closing->receptionist?->name}}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Opening Amount
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ number_format($closing->opening_amount, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Closing Amount
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ number_format($closing->closing_amount, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dd class="col-span-5 text-sm text-gray-900 dark:text-gray-100 flex flex-row space-y-2">
                    <strong className="text-sm font-medium text-gray-500 dark:text-gray-400 pr-2">Cash:</strong> {{ number_format($closing->cash_received, 2) }}
                    <strong className="text-sm font-medium text-gray-500 dark:text-gray-400 pr-2">Cheque:</strong> {{ number_format($closing->cheque_received, 2) }}
                    <strong className="text-sm font-medium text-gray-500 dark:text-gray-400 pr-2">Card:</strong> {{ number_format($closing->card_received, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Cheque Received
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ number_format($closing->cheque_received, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Card Received
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ number_format($closing->card_received, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Expenses Paid
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ number_format($closing->expense_payed, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Amount Reported
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ number_format($closing->amount_reported, 2) }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Cash Receiving Time
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ optional($closing->cash_recieving_time)?->format('M d, Y g:i A') ?? '-' }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Closed At
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ optional($closing->closed_at)?->format('M d, Y g:i A') ?? '-' }}
                </dd>
            </div>
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Opened At
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{ $closing->created_at ? $closing->created_at->format('M d, Y g:i A') : '-' }}
                </dd>
            </div>
        </dl>
        <dl class="space-y-3">
            <div class="grid grid-cols-5 gap-3 border-b border-gray-600 dark:border-gray-700 pb-2">
                <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                    Transactions Count
                </dt>
                <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                    {{$transactions->count()}}
                </dd>
            </div>
        </dl>
    </div>

    <div class="space-y-4 lg:col-span-2">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Closing Statement</h3>

        @if ($printUrl)
            <div class="overflow-hidden rounded-lg border border-gray-200 dark:border-gray-700">
                <iframe
                    src="{{ $printUrl }}"
                    title="Closing Statement"
                    class="h-[600px] w-full"
                    loading="lazy"
                ></iframe>
            </div>
        @else
            <p class="text-sm text-gray-500 dark:text-gray-400">
                Closing statement preview unavailable.
            </p>
        @endif
    </div>
</div>
