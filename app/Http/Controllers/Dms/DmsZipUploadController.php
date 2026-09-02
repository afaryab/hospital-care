<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dms\StoreDmsZipUploadRequest;
use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Services\Dms\DmsZipService;
use Illuminate\Http\RedirectResponse;

class DmsZipUploadController extends Controller
{
    public function store(StoreDmsZipUploadRequest $request, DmsZipService $service): RedirectResponse
    {
        $folder = DmsFolder::where('uuid', $request->validated('folder_uuid'))->firstOrFail();

        $this->authorize('create', [DmsDocument::class, $folder]);

        $service->extractZipSafely($request->file('file'), $folder, $request->user());

        return back();
    }
}
