<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Models\DmsDocument;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DocumentDownloadController extends Controller
{
    public function __invoke(Request $request, DmsDocument $document): BinaryFileResponse
    {
        $this->authorize('view', $document);

        $media = $document->currentVersionMedia();

        if (! $media) {
            abort(404);
        }

        activity()
            ->causedBy($request->user())
            ->performedOn($document)
            ->event('downloaded')
            ->log('Document downloaded');

        return response()->download($media->getPath(), $document->name);
    }
}
