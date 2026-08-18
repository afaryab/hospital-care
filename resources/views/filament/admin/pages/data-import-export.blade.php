@php
    $entry = $this->getSelectedEntry();
@endphp

<x-filament-panels::page>
    <x-filament::section>
        <div class="max-w-md">
            <label for="recordType" class="text-sm font-medium text-gray-700 dark:text-gray-200">
                Record Type
            </label>
            <select
                id="recordType"
                wire:model.live="recordType"
                class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-primary-500 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
            >
                <option value="">Select a record type…</option>
                @foreach ($this->getRecordTypeOptions() as $key => $label)
                    <option value="{{ $key }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>

        @if ($entry)
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                Use <span class="font-medium text-gray-700 dark:text-gray-300">Import {{ $entry['label'] }}</span>
                above to upload a spreadsheet and map its columns, or
                <span class="font-medium text-gray-700 dark:text-gray-300">Export {{ $entry['label'] }}</span>
                to download the current records.
            </p>
        @else
            <p class="mt-4 text-sm text-gray-500 dark:text-gray-400">
                Pick a record type to reveal its Import and Export actions above.
            </p>
        @endif
    </x-filament::section>
</x-filament-panels::page>
