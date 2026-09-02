<?php

namespace App\Http\Controllers\Dms;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dms\CopyDmsFolderRequest;
use App\Http\Requests\Dms\MoveDmsFolderRequest;
use App\Http\Requests\Dms\RenameDmsFolderRequest;
use App\Http\Requests\Dms\StoreDmsFolderRequest;
use App\Models\DmsFolder;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\RedirectResponse;

class DmsFolderController extends Controller
{
    public function store(StoreDmsFolderRequest $request, DmsFolderService $service): RedirectResponse
    {
        $parent = $request->validated('parent_uuid')
            ? DmsFolder::where('uuid', $request->validated('parent_uuid'))->firstOrFail()
            : null;

        $this->authorize('create', [DmsFolder::class, $parent]);

        $service->create(
            $request->validated('name'),
            $parent,
            $request->user(),
            $request->validated('classification_id'),
        );

        return back();
    }

    public function update(RenameDmsFolderRequest $request, DmsFolder $folder, DmsFolderService $service): RedirectResponse
    {
        $this->authorize('update', $folder);

        $service->rename($folder, $request->validated('name'));

        return back();
    }

    public function destroy(DmsFolder $folder, DmsFolderService $service): RedirectResponse
    {
        $this->authorize('delete', $folder);

        $service->delete($folder);

        return back();
    }

    public function move(MoveDmsFolderRequest $request, DmsFolder $folder, DmsFolderService $service): RedirectResponse
    {
        $target = DmsFolder::where('uuid', $request->validated('target_uuid'))->firstOrFail();

        $this->authorize('update', $folder);
        $this->authorize('create', [DmsFolder::class, $target]);

        $service->move($folder, $target);

        return back();
    }

    public function copy(CopyDmsFolderRequest $request, DmsFolder $folder, DmsFolderService $service): RedirectResponse
    {
        $target = DmsFolder::where('uuid', $request->validated('target_uuid'))->firstOrFail();

        $this->authorize('view', $folder);
        $this->authorize('create', [DmsFolder::class, $target]);

        $service->copy($folder, $target, $request->user());

        return back();
    }
}
