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

<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
    <div class="space-y-4">
        <h3 class="text-base font-semibold text-gray-900 dark:text-gray-100">Closing Details</h3>
        <dl class="space-y-3">
            @foreach ($details as $label => $value)
                <div class="grid grid-cols-5 gap-3">
                    <dt class="col-span-2 text-sm font-medium text-gray-500 dark:text-gray-400">
                        {{ $label }}
                    </dt>
                    <dd class="col-span-3 text-sm text-gray-900 dark:text-gray-100">
                        {{ $value }}
                    </dd>
                </div>
            @endforeach
        </dl>
    </div>

    <div class="space-y-4">
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
