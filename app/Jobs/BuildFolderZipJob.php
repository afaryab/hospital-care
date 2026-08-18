<?php

namespace App\Jobs;

use App\Models\DmsFolder;
use App\Models\User;
use App\Notifications\FolderZipReadyNotification;
use App\Services\Dms\DmsZipService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Builds a folder's zip off the request/response cycle for folders too
 * large to zip synchronously (see DmsZipService::fitsSyncThreshold()), then
 * notifies the requester with a signed, time-boxed download link.
 */
class BuildFolderZipJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;

    public int $timeout = 900;

    public function __construct(public int $folderId, public int $requestedByUserId) {}

    public function handle(DmsZipService $zips): void
    {
        $folder = DmsFolder::query()->find($this->folderId);
        $user = User::query()->find($this->requestedByUserId);

        if (! $folder || ! $user) {
            return;
        }

        $absolutePath = $zips->buildZipForFolder($folder);

        $user->notify(new FolderZipReadyNotification($folder->name, basename($absolutePath)));
    }
}
