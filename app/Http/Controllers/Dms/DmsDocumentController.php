<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dms\CopyDmsDocumentRequest;
use App\Http\Requests\Dms\MoveDmsDocumentRequest;
use App\Http\Requests\Dms\RenameDmsDocumentRequest;
use App\Http\Requests\Dms\StoreDmsDocumentRequest;
use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Services\Dms\DmsDocumentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DmsDocumentController extends Controller
{
    public function store(StoreDmsDocumentRequest $request, DmsDocumentService $service): RedirectResponse
    {
        $folder = DmsFolder::where('uuid', $request->validated('folder_uuid'))->firstOrFail();

        $this->authorize('create', [DmsDocument::class, $folder]);

        $service->upload(
            $request->file('file'),
            $folder,
            $request->user(),
            null,
            $request->validated('classification_id'),
        );

        return back();
    }

    public function update(RenameDmsDocumentRequest $request, DmsDocument $document, DmsDocumentService $service): RedirectResponse
    {
        $this->authorize('update', $document);

        $service->rename($document, $request->validated('name'));

        return back();
    }

    public function destroy(DmsDocument $document, DmsDocumentService $service): RedirectResponse
    {
        $this->authorize('delete', $document);

        $service->delete($document);

        return back();
    }

    public function move(MoveDmsDocumentRequest $request, DmsDocument $document, DmsDocumentService $service): RedirectResponse
    {
        $target = DmsFolder::where('uuid', $request->validated('target_uuid'))->firstOrFail();

        $this->authorize('update', $document);
        $this->authorize('create', [DmsFolder::class, $target]);

        $service->move($document, $target);

        return back();
    }

    public function copy(CopyDmsDocumentRequest $request, DmsDocument $document, DmsDocumentService $service): RedirectResponse
    {
        $target = DmsFolder::where('uuid', $request->validated('target_uuid'))->firstOrFail();

        $this->authorize('view', $document);
        $this->authorize('create', [DmsFolder::class, $target]);

        $service->copy($document, $target, $request->user());

        return back();
    }

    public function lock(Request $request, DmsDocument $document, DmsDocumentService $service): RedirectResponse
    {
        $this->authorize('update', $document);

        $service->lock($document, $request->user());

        return back();
    }

    public function unlock(Request $request, DmsDocument $document, DmsDocumentService $service): RedirectResponse
    {
        $this->authorize('update', $document);

        $service->unlock($document, $request->user());

        return back();
    }
}
