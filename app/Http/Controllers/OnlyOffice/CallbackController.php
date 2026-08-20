<?php

namespace App\Http\Controllers\OnlyOffice;

use App\Http\Controllers\Controller;
use App\Models\DmsDocument;
use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\OnlyOffice\OnlyOfficeConfigService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

/**
 * OnlyOffice's save callback. Sent by the Document Server (never the
 * browser) whenever a co-editing session finishes. Must respond with the
 * exact `{"error":0}` shape OnlyOffice's protocol expects, or the Document
 * Server treats the save as failed and retries indefinitely.
 *
 * https://api.onlyoffice.com/docs/docs-api/get-started/callback-handlers/
 */
class CallbackController extends Controller
{
    /**
     * OnlyOffice status codes that mean "the document is ready to be saved".
     */
    protected const SAVEABLE_STATUSES = [2, 6];

    public function __invoke(Request $request, DmsDocument $document, OnlyOfficeConfigService $configs, DmsDocumentService $documents): JsonResponse
    {
        if (! $request->hasValidSignature() || ! $configs->verifyToken($request)) {
            return response()->json(['error' => 1]);
        }

        $status = (int) $request->input('status');

        if (in_array($status, self::SAVEABLE_STATUSES, true)) {
            $actor = User::query()->find((int) $request->query('actor')) ?? $document->creator;
            $downloadUrl = (string) $request->input('url');

            if ($downloadUrl === '' || $actor === null) {
                return response()->json(['error' => 1]);
            }

            $tmpPath = tempnam(sys_get_temp_dir(), 'oo-save');
            $response = Http::timeout(60)->get($downloadUrl);

            if (! $response->successful()) {
                @unlink($tmpPath);

                return response()->json(['error' => 1]);
            }

            file_put_contents($tmpPath, $response->body());

            $documents->addVersion($document, $tmpPath, $actor, 'Saved from OnlyOffice');

            activity()
                ->causedBy($actor)
                ->performedOn($document)
                ->event('edited')
                ->log('Document saved from OnlyOffice');

            @unlink($tmpPath);
        }

        return response()->json(['error' => 0]);
    }
}
