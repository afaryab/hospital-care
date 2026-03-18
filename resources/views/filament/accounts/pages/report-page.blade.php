<x-filament-panels::page>
    <div class="flex gap-6">
        {{-- Left: Filter Form --}}
        <div class="w-72 shrink-0">
            <form wire:submit.prevent="applyFilters">
                {{ $this->filtersForm }}
                <div class="mt-4 flex gap-2">
                    <x-filament::button type="submit" size="sm" class="w-full">
                        Apply Filters
                    </x-filament::button>
                </div>
            </form>
        </div>

        {{-- Right: Tabs --}}
        <div class="flex-1 min-w-0">
            <x-filament::tabs>
                <x-filament::tabs.item
                    :active="$activeTab === 'general'"
                    wire:click="$set('activeTab', 'general')"
                    icon="heroicon-m-table-cells"
                >
                    General
                </x-filament::tabs.item>

                <x-filament::tabs.item
                    :active="$activeTab === 'pdf'"
                    wire:click="$set('activeTab', 'pdf')"
                    icon="heroicon-m-document"
                >
                    PDF
                </x-filament::tabs.item>
            </x-filament::tabs>

            <div class="mt-4">
                @if($activeTab === 'general')
                    {{ $this->table }}
                @else
                    <div class="rounded-xl border border-gray-200 dark:border-gray-700 overflow-hidden" style="height: 80vh;">
                        <iframe
                            src="{{ $this->getPdfUrl() }}"
                            class="w-full h-full"
                            frameborder="0"
                        ></iframe>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-filament-panels::page>
