<?php

namespace App\Policies;

use App\Models\SlipPhoto;
use App\Models\User;

class SlipPhotoPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin() || $user->hasRole('administrator')) {
            return true;
        }

        return null;
    }

    /**
     * The receptionist who took it, or anyone allowed to view the slip it is
     * attached to.
     */
    public function view(User $user, SlipPhoto $slipPhoto): bool
    {
        if ($slipPhoto->captured_by === $user->id) {
            return true;
        }

        return $slipPhoto->transaction !== null && $user->can('view', $slipPhoto->transaction);
    }

    /**
     * Only the receptionist who took it may relabel it, and only while it is
     * still pending — once on a slip it is evidence and stays as captured.
     */
    public function update(User $user, SlipPhoto $slipPhoto): bool
    {
        return $slipPhoto->captured_by === $user->id && $slipPhoto->transaction_id === null;
    }
}
