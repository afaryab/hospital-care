<div class="flex gap-2 border-b border-gray-200 dark:border-gray-700 mb-4">
    @foreach ($tabs as $key => $tab)
        <button
            type="button"
            @if(isset($setTabAction))
                wire:click="{{ $setTabAction }}('{{ $key }}')"
            @else
                wire:click="$set('activeTab', '{{ $key }}')"
            @endif
            @class([
                'px-4 py-2 font-medium rounded-t-md focus:outline-none',
                'bg-white dark:bg-gray-900 border-x border-t border-gray-200 dark:border-gray-700 text-primary-600 dark:text-primary-400' => $active === $key,
                'text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400' => $active !== $key,
            ])
        >
            {{ $tab['label'] }}
            @if(isset($tab['count']))
                <span class="ml-1 inline-block text-xs bg-gray-100 dark:bg-gray-800 rounded px-2 py-0.5">{{ $tab['count'] }}</span>
            @endif
        </button>
    @endforeach
</div>
