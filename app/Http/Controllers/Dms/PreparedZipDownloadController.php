<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Downloads a zip previously built by BuildFolderZipJob, linked from
 * FolderZipReadyNotification. Signed-route only — the requester may not be
 * mid-session when the job finishes.
 */
class PreparedZipDownloadController extends Controller
{
    public function __invoke(Request $request, string $filename): BinaryFileResponse
    {
        if (! $request->hasValidSignature()) {
            abort(403);
        }

        // The signed URL is the only input here, but defend against path
        // traversal in $filename regardless — never trust route params to
        // stay inside their expected directory.
        $safeName = basename($filename);
        if ($safeName !== $filename || ! Str::endsWith($safeName, '.zip')) {
            abort(404);
        }

        $path = Storage::disk('local')->path(config('dms.tmp_path').'/'.$safeName);

        if (! is_file($path)) {
            abort(404);
        }

        return response()->download($path, $safeName)->deleteFileAfterSend();
    }
}
