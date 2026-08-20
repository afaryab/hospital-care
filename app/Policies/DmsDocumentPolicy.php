<?php

namespace App\Policies;

use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Models\DmsShare;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class DmsDocumentPolicy
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

    public function view(User $user, DmsDocument $document): bool
    {
        if ($document->created_by === $user->id) {
            return true;
        }

        if ($this->hasDirectShare($user, $document, ['view', 'edit', 'manage'])) {
            return true;
        }

        return Gate::forUser($user)->allows('view', $document->folder);
    }

    public function create(User $user, DmsFolder $folder): bool
    {
        return Gate::forUser($user)->allows('create', [DmsFolder::class, $folder]);
    }

    public function update(User $user, DmsDocument $document): bool
    {
        if ($document->is_locked && $document->locked_by !== $user->id) {
            return false;
        }

        if ($document->created_by === $user->id) {
            return true;
        }

        if ($this->hasDirectShare($user, $document, ['edit', 'manage'])) {
            return true;
        }

        return Gate::forUser($user)->allows('update', $document->folder);
    }

    public function delete(User $user, DmsDocument $document): bool
    {
        if ($document->created_by === $user->id) {
            return true;
        }

        return Gate::forUser($user)->allows('delete', $document->folder);
    }

    public function share(User $user, DmsDocument $document): bool
    {
        if ($document->created_by === $user->id) {
            return true;
        }

        return Gate::forUser($user)->allows('share', $document->folder);
    }

    /**
     * @param  array<int, string>  $abilities
     */
    protected function hasDirectShare(User $user, DmsDocument $document, array $abilities): bool
    {
        return DmsShare::query()
            ->where('document_id', $document->id)
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
