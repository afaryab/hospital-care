<div class="flex flex-col space-y-1">
    <div class="flex items-center space-x-2">
        @if($record->email_verified_at)
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                <x-heroicon-s-check class="w-3 h-3 mr-1" />
                Verified
            </span>
        @else
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200">
                <x-heroicon-s-exclamation class="w-3 h-3 mr-1" />
                Unverified
            </span>
        @endif
    </div>
    
    <div class="flex items-center space-x-2">
        @if($record->two_factor_confirmed_at)
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                <x-heroicon-s-shield-check class="w-3 h-3 mr-1" />
                2FA Enabled
            </span>
        @else
            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                <x-heroicon-s-shield-exclamation class="w-3 h-3 mr-1" />
                2FA Disabled
            </span>
        @endif
    </div>
</div>