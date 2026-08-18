<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Models\DmsShare;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Consumed by the link in a "share this document by email" notification —
 * reachable without an app session (the recipient may not even have an
 * account), gated purely by the signed URL and the share's own expiry.
 */
class PublicShareDownloadController extends Controller
{
    public function __invoke(Request $request, DmsShare $share): BinaryFileResponse
    {
        if (! $request->hasValidSignature() || $share->isExpired() || ! $share->document_id) {
            abort(403);
        }

        $document = $share->document;
        $media = $document?->currentVersionMedia();

        if (! $media) {
            abort(404);
        }

        $share->markAccessed();

        activity()
            ->performedOn($document)
            ->event('downloaded')
            ->withProperties(['via' => 'share_link', 'share_id' => $share->id])
            ->log('Document downloaded via shared link');

        return response()->download($media->getPath(), $document->name);
    }
}
