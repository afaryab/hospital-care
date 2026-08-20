<?php

namespace App\Http\Controllers\OnlyOffice;

use App\Http\Controllers\Controller;
use App\Models\DmsDocument;
use App\Services\OnlyOffice\OnlyOfficeConfigService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class EditorPageController extends Controller
{
    public function __invoke(Request $request, DmsDocument $document, OnlyOfficeConfigService $configs): View
    {
        $this->authorize('update', $document);

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
