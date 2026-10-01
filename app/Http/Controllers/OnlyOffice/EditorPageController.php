<?php

namespace App\Http\Controllers\OnlyOffice;

use App\Http\Controllers\Controller;
use App\Models\DmsDocument;
use App\Services\OnlyOffice\OnlyOfficeConfigService;
use App\Services\OnlyOffice\OnlyOfficeHealth;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EditorPageController extends Controller
{
    public function __invoke(Request $request, DmsDocument $document, OnlyOfficeConfigService $configs, OnlyOfficeHealth $health): View|Response
    {
        $this->authorize('update', $document);

        if (! $health->available()) {
            return response()->view('onlyoffice.unavailable', [
                'documentName' => $document->name,
                'backUrl' => route('dms.index', $document->folder),
            ], 503);
        }

        $config = $configs->editorConfig($document, $request->user());

        activity()
            ->causedBy($request->user())
            ->performedOn($document)
            ->event('opened')
            ->log('Document opened in OnlyOffice editor');

        return view('onlyoffice.editor', [
            'config' => $config,
            'apiUrl' => rtrim(config('onlyoffice.public_path'), '/').'/web-apps/apps/api/documents/api.js',
        ]);
    }
}
