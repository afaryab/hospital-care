<x-filament-panels::page>
    <div class="flex flex-col md:flex-row space-y-6 space-x-0 md:space-y-0 md:space-x-6">
        <div class="flex-0 w-3/12">
            <form wire:submit.prevent="viewReport">
                {{ $this->form }}
            </form>
        </div>
        <div class="flex-1 w-9/12 overflow-y-scroll h-[70vh] p-2 bg-gray-50">
                <div class="w-full h-full border shadow-lg rounded-lg bg-white">
                    @if(request()->method() === 'POST')
                    <iframe 
                        src="{{ route('trial-balance.stream-pdf', [
                            'yearId' => $yearId,
                            'startDate' => $startDate,
                            'endDate' => $endDate,
                        ]) }}" 
                        class="w-full h-full border-0" 
                        style="height: calc(100% - 60px);">
                    </iframe>
                    @endif
                </div>
        </div>
    </div>
</x-filament-panels::page>
