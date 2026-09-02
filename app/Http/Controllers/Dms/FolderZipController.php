<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Jobs\BuildFolderZipJob;
use App\Models\DmsFolder;
use App\Services\Dms\DmsZipService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class FolderZipController extends Controller
{
    public function __invoke(Request $request, DmsFolder $folder, DmsZipService $zips): BinaryFileResponse|RedirectResponse
    {
        $this->authorize('view', $folder);

        activity()
            ->causedBy($request->user())
            ->performedOn($folder)
            ->event('zipped')
            ->log('Folder downloaded as zip');

        if ($zips->fitsSyncThreshold($folder)) {
            $path = $zips->buildZipForFolder($folder);

            return response()->download($path, basename($path))->deleteFileAfterSend();
        }

        BuildFolderZipJob::dispatch($folder->id, $request->user()->id);

        return back()->with('status', "This folder is large — we're preparing the zip and will email you a download link.");
    }
}
