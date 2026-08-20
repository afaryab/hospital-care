<?php

namespace App\Services\Dms;

use App\Models\DmsFolder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DmsFolderService
{
    public function create(string $name, ?DmsFolder $parent, User $actor, ?int $classificationId = null): DmsFolder
    {
        $this->assertUniqueSiblingName($name, $parent);

        return DB::transaction(function () use ($name, $parent, $actor, $classificationId) {
            $folder = DmsFolder::create([
                'name' => $name,
                'parent_id' => $parent?->id,
                'path' => $parent ? $parent->path.$parent->id.'/' : '/',
                'classification_id' => $classificationId,
                'created_by' => $actor->id,
            ]);

            return $folder;
        });
    }

    public function rename(DmsFolder $folder, string $name): DmsFolder
    {
        if ($folder->is_system) {
            throw ValidationException::withMessages(['name' => 'System folders cannot be renamed.']);
        }

        $this->assertUniqueSiblingName($name, $folder->parent, $folder);

        $folder->update(['name' => $name]);

        return $folder->fresh();
    }

    public function move(DmsFolder $folder, DmsFolder $target): DmsFolder
    {
        if ($folder->is_system) {
            throw ValidationException::withMessages(['folder' => 'System folders cannot be moved.']);
        }

        if ($target->isDescendantOf($folder)) {
            throw ValidationException::withMessages(['target' => 'A folder cannot be moved into itself or one of its own subfolders.']);
        }

        $this->assertUniqueSiblingName($folder->name, $target, $folder);

        return DB::transaction(function () use ($folder, $target) {
            // $oldPrefix is what every descendant's path currently starts
            // with (the moved folder's own ancestor-path segment included).
            // $newPrefix is the same shape after the move — note this is
            // NOT the same as $folder's own new `path` value, which holds
            // $folder's ancestors and deliberately excludes $folder's own
            // id; descendants' paths must include it.
            $oldPrefix = $folder->path.$folder->id.'/';
            $newPath = $target->path.$target->id.'/';
            $newPrefix = $newPath.$folder->id.'/';

            $descendants = $folder->descendantsQuery()->lockForUpdate()->get();

            $folder->update(['parent_id' => $target->id, 'path' => $newPath]);

            foreach ($descendants as $descendant) {
                $descendant->update([
                    'path' => $newPrefix.substr($descendant->path, strlen($oldPrefix)),
                ]);
            }

            return $folder->fresh();
        });
    }

    /**
     * Deep-copies a folder subtree (and every document inside it) into
     * $target. Media files are physically duplicated, not referenced — an
     * explicit storage-cost tradeoff over building copy-on-write semantics.
     */
    public function copy(DmsFolder $folder, DmsFolder $target, User $actor): DmsFolder
    {
        if ($target->isDescendantOf($folder)) {
            throw ValidationException::withMessages(['target' => 'A folder cannot be copied into itself or one of its own subfolders.']);
        }

        return DB::transaction(fn () => $this->copyRecursive($folder, $target, $actor));
    }

    protected function copyRecursive(DmsFolder $folder, DmsFolder $target, User $actor): DmsFolder
    {
        $name = $this->uniqueSiblingName($folder->name, $target);

        $copy = DmsFolder::create([
            'name' => $name,
            'parent_id' => $target->id,
            'path' => $target->path.$target->id.'/',
            'classification_id' => $folder->classification_id,
            'created_by' => $actor->id,
        ]);

        $documentService = app(DmsDocumentService::class);
        foreach ($folder->documents()->get() as $document) {
            $documentService->copy($document, $copy, $actor);
        }

        foreach ($folder->children()->get() as $child) {
            $this->copyRecursive($child, $copy, $actor);
        }

        return $copy;
    }

    /**
     * Blocks deletion of system folders and non-empty folders, mirroring the
     * "Cascade Protection" rule — a folder with children or documents must
     * have them removed/moved first, deletion never cascades.
     */
    public function delete(DmsFolder $folder): void
    {
        if ($folder->is_system) {
            throw ValidationException::withMessages(['folder' => 'System folders cannot be deleted.']);
        }

        if ($folder->children()->exists() || $folder->documents()->exists()) {
            throw ValidationException::withMessages(['folder' => 'Only empty folders can be deleted. Move or delete its contents first.']);
        }

        $folder->delete();
    }

    protected function assertUniqueSiblingName(string $name, ?DmsFolder $parent, ?DmsFolder $ignore = null): void
    {
        $query = DmsFolder::query()
            ->where('parent_id', $parent?->id)
            ->where('name', $name);

        if ($ignore) {
            $query->whereKeyNot($ignore->id);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages(['name' => 'A folder with this name already exists here.']);
        }
    }

    protected function uniqueSiblingName(string $name, DmsFolder $parent): string
    {
        $candidate = $name;
        $suffix = 1;

        while (DmsFolder::query()->where('parent_id', $parent->id)->where('name', $candidate)->exists()) {
            $suffix++;
            $candidate = "{$name} ({$suffix})";
        }

        return $candidate;
    }
}
