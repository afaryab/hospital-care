<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Models\DmsClassification;
use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Services\Dms\DmsProvisioningService;
use Inertia\Inertia;
use Inertia\Response;

/**
 * The React "Drive" browser: renders the current folder's children (or the
 * top-level folders when no folder is open) plus the breadcrumb trail and
 * the flat folder list used by the move/copy target picker.
 */
class DmsBrowserController extends Controller
{
    public function index(?DmsFolder $folder = null): Response
    {
        $this->authorize($folder ? 'view' : 'viewAny', $folder ?? DmsFolder::class);

        if (! $folder) {
            app(DmsProvisioningService::class)->patientsRoot();
            app(DmsProvisioningService::class)->doctorsRoot();
        }

        return Inertia::render('dms/index', [
            'folder' => $folder,
            'breadcrumbs' => $this->breadcrumbs($folder),
            'folders' => DmsFolder::query()
                ->where('parent_id', $folder?->id)
                ->with('classification')
                ->orderBy('name')
                ->get(),
            'documents' => $folder
                ? DmsDocument::query()
                    ->where('folder_id', $folder->id)
                    ->with(['classification', 'lockedBy'])
                    ->orderBy('name')
                    ->get()
                : collect(),
            'folderOptions' => DmsFolder::query()
                ->orderBy('path')
                ->orderBy('name')
                ->get()
                ->map(fn (DmsFolder $option) => [
                    'uuid' => $option->uuid,
                    'label' => $option->fullPathLabel(),
                ])
                ->values(),
            'classifications' => DmsClassification::cachedAll()->values(),
        ]);
    }

    /**
     * @return array<int, DmsFolder>
     */
    private function breadcrumbs(?DmsFolder $folder): array
    {
        $trail = [];

        while ($folder !== null) {
            array_unshift($trail, $folder);
            $folder = $folder->parent;
        }

        return $trail;
    }
}
