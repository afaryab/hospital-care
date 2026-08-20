<?php

namespace App\Http\Controllers\OnlyOffice;

use App\Http\Controllers\Controller;
use App\Models\DmsDocument;
use App\Services\OnlyOffice\OnlyOfficeConfigService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves the raw file bytes when the OnlyOffice Document Server fetches the
 * document to edit. Reached only from the container itself, never the
 * browser — authenticated via a signed URL plus the OnlyOffice JWT, not a
 * session (there is no browser cookie on this request).
 */
class DocumentContentController extends Controller
{
    public function __invoke(Request $request, DmsDocument $document, OnlyOfficeConfigService $configs): BinaryFileResponse
    {
        if (! $request->hasValidSignature() || ! $configs->verifyToken($request)) {
            abort(403);
        }

        $media = $document->currentVersionMedia();

        if (! $media) {
            abort(404);
        }

        return response()->file($media->getPath(), [
            'Content-Type' => $media->mime_type ?? 'application/octet-stream',
        ]);
    }
}
