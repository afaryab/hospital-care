<div class="user-details-container">
    <div class="user-details-name">{{ $user->name }}</div>
    <div class="user-details-email">{{ $user->email }}</div>
    @if($user->banned_message)
        <div class="user-details-banned-message">
            {{ $user->banned_message }}
        </div>
    @endif
</div>