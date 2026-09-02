<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dms\StoreDmsShareRequest;
use App\Models\DmsDocument;
use App\Models\DmsShare;
use App\Notifications\DocumentSharedNotification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Notification;

class DmsShareController extends Controller
{
    public function store(StoreDmsShareRequest $request, DmsDocument $document): RedirectResponse
    {
        $this->authorize('share', $document);

        $share = DmsShare::query()->create([
            'document_id' => $document->id,
            'grantee_type' => DmsShare::GRANTEE_EMAIL,
            'grantee_value' => $request->validated('email'),
            'ability' => $request->validated('ability'),
            'expires_at' => now()->addDays((int) config('dms.share_link_expires_days')),
            'created_by' => $request->user()->id,
        ]);

        Notification::route('mail', $request->validated('email'))
            ->notify(new DocumentSharedNotification($document, $share, $request->user()));

        activity()->causedBy($request->user())->performedOn($document)->event('shared')
            ->withProperties(['to' => $request->validated('email')])
            ->log('Document shared by email');

        return back();
    }
}
