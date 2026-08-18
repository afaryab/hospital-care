@php
    $stats = $this->getCollectiveStats();
    $entries = $this->getEntries();
@endphp

<x-filament-panels::page>
    {{-- Collective stats --}}
    <div class="grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Cache Driver</p>
            <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ ucfirst($stats['driver']) }}</p>
            <span
                @class([
                    'mt-2 inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium',
                    'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' => $stats['connected'],
                    'bg-danger-50 text-danger-700 dark:bg-danger-500/10 dark:text-danger-400' => ! $stats['connected'],
                ])
            >
                <span @class(['h-1.5 w-1.5 rounded-full', 'bg-success-500' => $stats['connected'], 'bg-danger-500' => ! $stats['connected']])></span>
                {{ $stats['connected'] ? 'Connected' : 'Unreachable' }}
            </span>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Models Cached</p>
            <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $stats['models_cached'] }} / {{ $stats['models_total'] }}</p>
            <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                <div
                    class="h-full rounded-full bg-primary-500"
                    style="width: {{ $stats['models_total'] > 0 ? round(($stats['models_cached'] / $stats['models_total']) * 100) : 0 }}%"
                ></div>
            </div>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Cached Records</p>
            <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ number_format($stats['records_cached']) }}</p>
            <p class="mt-2 text-xs text-gray-400">across all cached models</p>
        </div>

        <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-xs font-medium text-gray-500 dark:text-gray-400">Redis Memory Used</p>
            <p class="mt-1 text-lg font-semibold text-gray-950 dark:text-white">{{ $stats['memory_used'] ?? '—' }}</p>
            <p class="mt-2 text-xs text-gray-400">{{ $stats['memory_used'] ? 'whole instance' : 'not on redis driver' }}</p>
        </div>
    </div>

    {{-- Per-model cache cards --}}
    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-3">
        @foreach ($entries as $entry)
            @php
                $percent = $entry['total_count'] > 0 ? min(100, round(($entry['cached_count'] / $entry['total_count']) * 100)) : ($entry['is_cached'] ? 100 : 0);
            @endphp
            <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm dark:border-gray-700 dark:bg-gray-800" wire:key="cache-card-{{ $entry['key'] }}">
                <div class="flex items-start justify-between gap-2">
                    <p class="font-medium text-gray-950 dark:text-white">{{ $entry['label'] }}</p>
                    <span
                        @class([
                            'inline-flex items-center gap-1 rounded-full px-2 py-0.5 text-xs font-medium whitespace-nowrap',
                            'bg-success-50 text-success-700 dark:bg-success-500/10 dark:text-success-400' => $entry['is_cached'],
                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => ! $entry['is_cached'],
                        ])
                    >
                        {{ $entry['is_cached'] ? 'Cached' : 'Not Cached' }}
                    </span>
                </div>

                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                    {{ number_format($entry['cached_count']) }} cached of {{ number_format($entry['total_count']) }} total
                </p>

                {{-- Minichart: cached vs total --}}
                <div class="mt-2 h-1.5 w-full overflow-hidden rounded-full bg-gray-100 dark:bg-gray-700">
                    <div
                        @class([
                            'h-full rounded-full',
                            'bg-success-500' => $entry['is_cached'],
                            'bg-gray-300 dark:bg-gray-600' => ! $entry['is_cached'],
                        ])
                        style="width: {{ $percent }}%"
                    ></div>
                </div>

                <div class="mt-3 flex gap-2">
                    <button
                        type="button"
                        wire:click="clearModelCache('{{ $entry['key'] }}')"
                        wire:loading.attr="disabled"
                        class="flex-1 rounded-lg border border-gray-300 px-3 py-1.5 text-xs font-medium text-gray-700 transition-colors hover:bg-gray-50 disabled:opacity-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-700"
                    >
                        Clear Cache
                    </button>
                    <button
                        type="button"
                        wire:click="warmModelCache('{{ $entry['key'] }}')"
                        wire:loading.attr="disabled"
                        class="flex-1 rounded-lg bg-primary-600 px-3 py-1.5 text-xs font-medium text-white transition-colors hover:bg-primary-500 disabled:opacity-50"
                    >
                        Warm Cache
                    </button>
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
