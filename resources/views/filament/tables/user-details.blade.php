<div class="flex flex-col space-y-1">
    <div class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
    <div class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
    @if($user->banned_message)
        <div class="text-xs text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 px-2 py-1 rounded">
            {{ $user->banned_message }}
        </div>
    @endif
</div>