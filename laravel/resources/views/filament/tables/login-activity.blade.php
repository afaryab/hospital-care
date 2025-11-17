<div class="flex flex-col space-y-1 text-xs">
    @if($record->last_login)
        <div class="flex items-center space-x-1">
            <x-heroicon-s-clock class="w-3 h-3 text-green-500" />
            <span class="text-gray-600 dark:text-gray-300">
                Last: {{ $record->last_login->diffForHumans() }}
            </span>
        </div>
    @endif
    
    @if($record->ip_address)
        <div class="flex items-center space-x-1">
            <x-heroicon-s-globe-alt class="w-3 h-3 text-blue-500" />
            <span class="text-gray-600 dark:text-gray-300">{{ $record->ip_address }}</span>
        </div>
    @endif
    
    @if($record->login_attempts > 0)
        <div class="flex items-center space-x-1">
            <x-heroicon-s-exclamation-triangle class="w-3 h-3 text-yellow-500" />
            <span class="text-yellow-600 dark:text-yellow-400">
                {{ $record->login_attempts }} failed attempts
            </span>
        </div>
    @endif
</div>