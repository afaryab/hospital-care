<?php

namespace App\Policies;

use App\Models\DmsFolder;
use App\Models\DmsShare;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DmsFolderPolicy
{
    public function before(User $user): ?bool
    {
        if ($user->isAdmin() || $user->hasRole('administrator')) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->hasAnyProfile();
    }

    public function view(User $user, DmsFolder $folder): bool
    {
        return $this->hasAccess($user, $folder, ['view', 'edit', 'manage']);
    }

    public function create(User $user, ?DmsFolder $parent = null): bool
    {
        if ($parent === null) {
            return false;
        }

        return $this->hasAccess($user, $parent, ['edit', 'manage']);
    }

    public function update(User $user, DmsFolder $folder): bool
    {
        if ($folder->is_system) {
            return false;
        }

        return $this->hasAccess($user, $folder, ['edit', 'manage']);
    }

    public function delete(User $user, DmsFolder $folder): bool
    {
        if ($folder->is_system) {
            return false;
        }

        return $folder->created_by === $user->id || $this->hasAccess($user, $folder, ['manage']);
    }

    public function share(User $user, DmsFolder $folder): bool
    {
        return $folder->created_by === $user->id || $this->hasAccess($user, $folder, ['manage']);
    }

    /**
     * Access is granted to: the creator, a doctor viewing their own
     * system-linked doctor folder, anyone who already has Patient-view
     * access to a system-linked patient folder, or anyone covered by an
     * active share on this folder or one of its ancestors.
     */
    protected function hasAccess(User $user, DmsFolder $folder, array $abilities): bool
    {
        if ($folder->created_by === $user->id) {
            return true;
        }

        if ($folder->owner_type === User::class && (int) $folder->owner_id === $user->id) {
            return true;
        }

        if ($folder->owner_type === Patient::class && $folder->owner instanceof Patient) {
            if (Gate::forUser($user)->allows('view', $folder->owner)) {
                return true;
            }
        }

        return $this->hasActiveShare($user, $this->folderAndAncestorIds($folder), $abilities);
    }

    /**
     * This folder's id plus every ancestor id, read from the materialized
     * `path` column (e.g. "/1/4/9/") — a share granted on an ancestor
     * folder is inherited by everything nested beneath it.
     *
     * @return array<int, int>
     */
    protected function folderAndAncestorIds(DmsFolder $folder): array
    {
        $ancestorIds = array_map('intval', array_filter(explode('/', $folder->path), fn (string $part): bool => $part !== ''));
        $ancestorIds[] = $folder->id;

        return $ancestorIds;
    }

    /**
     * @param  array<int, int>  $folderIds
     * @param  array<int, string>  $abilities
     */
    protected function hasActiveShare(User $user, array $folderIds, array $abilities): bool
    {
        return DmsShare::query()
            ->whereIn('folder_id', $folderIds)
            ->whereIn('ability', $abilities)
            ->where(function ($query) use ($user) {
                $query->where(function ($grantee) use ($user) {
                    $grantee->where('grantee_type', DmsShare::GRANTEE_USER)
                        ->where('grantee_value', (string) $user->id);
                })->orWhere(function ($grantee) use ($user) {
                    $grantee->where('grantee_type', DmsShare::GRANTEE_ROLE)
                        ->whereIn('grantee_value', $user->getRoleNames());
                });
            })
            ->where(fn ($query) => $query->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->exists();
    }
}
