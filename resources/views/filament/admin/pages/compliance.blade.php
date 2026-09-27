@php
    $checks = collect($this->getChecks());
    $summary = app(\App\Services\Compliance\ComplianceService::class)->summary($checks->all());
    $failing = $summary['fail'] ?? 0;
    $attention = ($summary['warning'] ?? 0) + ($summary['pending'] ?? 0);
    $ok = ($summary['pass'] ?? 0) + ($summary['attested'] ?? 0);
    $score = $checks->count() > 0 ? round($ok / $checks->count() * 100) : 0;
    $badge = [
        'success' => 'bg-success-50 text-success-700 ring-success-600/20 dark:bg-success-500/10 dark:text-success-400',
        'warning' => 'bg-warning-50 text-warning-700 ring-warning-600/20 dark:bg-warning-500/10 dark:text-warning-400',
        'danger' => 'bg-danger-50 text-danger-700 ring-danger-600/20 dark:bg-danger-500/10 dark:text-danger-400',
    ];
@endphp

<x-filament-panels::page>
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Compliance score</p>
            <p class="mt-1 text-2xl font-semibold text-gray-950 dark:text-white">{{ $score }}%</p>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                <div class="h-full rounded-full bg-primary-500" style="width: {{ $score }}%"></div>
            </div>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Compliant / attested</p>
            <p class="mt-1 text-2xl font-semibold text-success-600">{{ $ok }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Needs attention</p>
            <p class="mt-1 text-2xl font-semibold text-warning-600">{{ $attention }}</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Non-compliant</p>
            <p class="mt-1 text-2xl font-semibold text-danger-600">{{ $failing }}</p>
        </div>
    </div>

    @foreach ($checks->groupBy('category') as $category => $items)
        <section class="space-y-3">
            <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">{{ $category }}</h2>

            <div class="divide-y divide-gray-200 overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm dark:divide-gray-700 dark:border-gray-700 dark:bg-gray-800">
                @foreach ($items as $check)
                    <div class="flex flex-col gap-3 p-4 sm:flex-row sm:items-start" wire:key="compliance-{{ $check['key'] }}">
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="font-medium text-gray-950 dark:text-white">{{ $check['title'] }}</h3>
                                <span class="text-xs text-gray-400">{{ $check['framework'] }}</span>
                            </div>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">{{ $check['description'] }}</p>
                            <p class="mt-2 text-sm text-gray-700 dark:text-gray-300">{{ $check['summary'] }}</p>

                            @if (! empty($check['issues']))
                                <ul class="mt-2 list-disc space-y-1 pl-5 text-sm text-gray-600 dark:text-gray-300">
                                    @foreach ($check['issues'] as $issue)
                                        <li>{{ $issue }}</li>
                                    @endforeach
                                </ul>
                            @endif

                            @if (! empty($check['attestation']['note']))
                                <p class="mt-2 text-xs text-gray-500 dark:text-gray-400">Evidence: {{ $check['attestation']['note'] }}</p>
                            @endif
                        </div>

                        <div class="flex shrink-0 items-center gap-2">
                            <span @class(['inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium ring-1 ring-inset', $badge[$check['status']->color()]])>
                                {{ $check['status']->label() }}
                            </span>
                            @if ($check['manual'])
                                {{ ($this->attestAction)(['key' => $check['key']]) }}
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
    @endforeach

</x-filament-panels::page>
