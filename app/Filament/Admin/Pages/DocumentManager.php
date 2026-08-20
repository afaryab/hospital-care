<?php

namespace App\Filament\Admin\Pages;

use App\Models\DmsClassification;
use App\Models\DmsDocument;
use App\Models\DmsFolder;
use App\Models\DmsShare;
use App\Notifications\DocumentSharedNotification;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use App\Services\Dms\DmsProvisioningService;
use App\Services\Dms\DmsZipService;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification as NotificationFacade;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\WithFileUploads;

/**
 * The admin-facing document explorer: nested folders, upload/rename/move/
 * copy/delete, zip download, email sharing, and safe zip-extract-into-folder.
 * State-changing actions are plain Livewire methods delegating to the Dms
 * services rather than Filament modal Actions — kept deliberately simple so
 * the underlying service calls (and their authorization/validation) stay
 * easy to follow and to test via Livewire::test(), see
 * DocumentManagerPageTest.
 */
class DocumentManager extends Page
{
    use WithFileUploads;

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFolderOpen;

    protected static ?int $navigationSort = 12;

    protected static ?string $navigationLabel = 'Documents';

    protected static ?string $title = 'Document Manager';

    protected string $view = 'filament.admin.pages.document-manager';

    public ?int $currentFolderId = null;

    #[Validate('required|string|max:255')]
    public string $newFolderName = '';

    public ?int $newFolderClassificationId = null;

    public ?int $renamingFolderId = null;

    public ?int $renamingDocumentId = null;

    public string $renameValue = '';

    public ?string $movingType = null;

    public ?int $movingId = null;

    public ?int $moveTargetFolderId = null;

    public ?string $copyingType = null;

    public ?int $copyingId = null;

    public ?int $copyTargetFolderId = null;

    public $uploadFile = null;

    public $zipUploadFile = null;

    public ?int $sharingDocumentId = null;

    public string $shareEmail = '';

    public string $shareAbility = 'view';

    public static function canAccess(): bool
    {
        return auth()->user()?->isAdmin() ?? false;
    }

    public function mount(): void
    {
        // Materializes the "Patients"/"Doctors" system roots on first visit
        // so they always appear at the top level, even before any patient
        // or doctor folder has been lazily created underneath them.
        app(DmsProvisioningService::class)->patientsRoot();
        app(DmsProvisioningService::class)->doctorsRoot();
    }

    public function currentFolder(): ?DmsFolder
    {
        return $this->currentFolderId ? DmsFolder::query()->find($this->currentFolderId) : null;
    }

    /**
     * @return array<int, DmsFolder>
     */
    public function breadcrumbs(): array
    {
        $trail = [];
        $folder = $this->currentFolder();

        while ($folder !== null) {
            array_unshift($trail, $folder);
            $folder = $folder->parent;
        }

        return $trail;
    }

    public function folders(): Collection
    {
        return DmsFolder::query()->where('parent_id', $this->currentFolderId)->orderBy('name')->get();
    }

    public function documents(): Collection
    {
        if (! $this->currentFolderId) {
            return collect();
        }

        return DmsDocument::query()->where('folder_id', $this->currentFolderId)->orderBy('name')->get();
    }

    /**
     * All folders, for the move/copy target picker.
     */
    public function folderOptions(): array
    {
        return DmsFolder::query()->orderBy('path')->orderBy('name')->get()
            ->mapWithKeys(fn (DmsFolder $folder) => [$folder->id => $folder->fullPathLabel()])
            ->toArray();
    }

    public function classificationOptions(): array
    {
        return DmsClassification::cachedAll()->pluck('name', 'id')->toArray();
    }

    public function isOfficeEditable(DmsDocument $document): bool
    {
        return in_array(strtolower(pathinfo($document->name, PATHINFO_EXTENSION)), [
            'doc', 'docx', 'odt', 'rtf', 'xls', 'xlsx', 'ods', 'csv', 'ppt', 'pptx', 'odp', 'txt',
        ], true);
    }

    public function openFolder(?int $folderId): void
    {
        $this->currentFolderId = $folderId;
    }

    public function createFolder(DmsFolderService $service): void
    {
        $this->validateOnly('newFolderName');

        try {
            $service->create($this->newFolderName, $this->currentFolder(), auth()->user(), $this->newFolderClassificationId);
            $this->newFolderName = '';
            $this->newFolderClassificationId = null;
            $this->notifySuccess('Folder created.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function startRenameFolder(int $folderId): void
    {
        $folder = DmsFolder::query()->findOrFail($folderId);
        $this->renamingFolderId = $folderId;
        $this->renamingDocumentId = null;
        $this->renameValue = $folder->name;
    }

    public function startRenameDocument(int $documentId): void
    {
        $document = DmsDocument::query()->findOrFail($documentId);
        $this->renamingDocumentId = $documentId;
        $this->renamingFolderId = null;
        $this->renameValue = $document->name;
    }

    public function confirmRename(DmsFolderService $folders, DmsDocumentService $documents): void
    {
        try {
            if ($this->renamingFolderId) {
                $folders->rename(DmsFolder::query()->findOrFail($this->renamingFolderId), $this->renameValue);
            } elseif ($this->renamingDocumentId) {
                $documents->rename(DmsDocument::query()->findOrFail($this->renamingDocumentId), $this->renameValue);
            }
            $this->cancelRename();
            $this->notifySuccess('Renamed.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function cancelRename(): void
    {
        $this->renamingFolderId = null;
        $this->renamingDocumentId = null;
        $this->renameValue = '';
    }

    public function deleteFolder(int $folderId, DmsFolderService $service): void
    {
        try {
            $service->delete(DmsFolder::query()->findOrFail($folderId));
            $this->notifySuccess('Folder deleted.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function deleteDocument(int $documentId, DmsDocumentService $service): void
    {
        $service->delete(DmsDocument::query()->findOrFail($documentId));
        $this->notifySuccess('Document deleted.');
    }

    public function startMove(string $type, int $id): void
    {
        $this->movingType = $type;
        $this->movingId = $id;
    }

    public function cancelMove(): void
    {
        $this->movingType = null;
        $this->movingId = null;
        $this->moveTargetFolderId = null;
    }

    public function confirmMove(DmsFolderService $folders, DmsDocumentService $documents): void
    {
        if (! $this->moveTargetFolderId) {
            return;
        }

        $target = DmsFolder::query()->findOrFail($this->moveTargetFolderId);

        try {
            if ($this->movingType === 'folder') {
                $folders->move(DmsFolder::query()->findOrFail($this->movingId), $target);
            } else {
                $documents->move(DmsDocument::query()->findOrFail($this->movingId), $target);
            }
            $this->cancelMove();
            $this->notifySuccess('Moved.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function startCopy(string $type, int $id): void
    {
        $this->copyingType = $type;
        $this->copyingId = $id;
    }

    public function cancelCopy(): void
    {
        $this->copyingType = null;
        $this->copyingId = null;
        $this->copyTargetFolderId = null;
    }

    public function confirmCopy(DmsFolderService $folders, DmsDocumentService $documents): void
    {
        if (! $this->copyTargetFolderId) {
            return;
        }

        $target = DmsFolder::query()->findOrFail($this->copyTargetFolderId);

        try {
            if ($this->copyingType === 'folder') {
                $folders->copy(DmsFolder::query()->findOrFail($this->copyingId), $target, auth()->user());
            } else {
                $documents->copy(DmsDocument::query()->findOrFail($this->copyingId), $target, auth()->user());
            }
            $this->cancelCopy();
            $this->notifySuccess('Copied.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function uploadDocument(DmsDocumentService $service): void
    {
        $this->validate(['uploadFile' => 'required|file|max:'.(200 * 1024)]);

        if (! $this->currentFolder()) {
            $this->notifyError('Open a folder before uploading.');

            return;
        }

        $service->upload($this->uploadFile, $this->currentFolder(), auth()->user());

        $this->uploadFile = null;
        $this->notifySuccess('Document uploaded.');
    }

    public function extractZip(DmsZipService $service): void
    {
        $this->validate(['zipUploadFile' => 'required|file|mimes:zip|max:'.(200 * 1024)]);

        if (! $this->currentFolder()) {
            $this->notifyError('Open a folder before extracting a zip.');

            return;
        }

        try {
            $documents = $service->extractZipSafely($this->zipUploadFile, $this->currentFolder(), auth()->user());
            $this->zipUploadFile = null;
            $this->notifySuccess(count($documents).' file(s) extracted.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function lockDocument(int $documentId, DmsDocumentService $service): void
    {
        try {
            $service->lock(DmsDocument::query()->findOrFail($documentId), auth()->user());
            $this->notifySuccess('Document locked for editing.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function unlockDocument(int $documentId, DmsDocumentService $service): void
    {
        try {
            $service->unlock(DmsDocument::query()->findOrFail($documentId), auth()->user());
            $this->notifySuccess('Document unlocked.');
        } catch (ValidationException $e) {
            $this->notifyError($e->getMessage());
        }
    }

    public function startShare(int $documentId): void
    {
        $this->sharingDocumentId = $documentId;
        $this->shareEmail = '';
        $this->shareAbility = 'view';
    }

    public function cancelShare(): void
    {
        $this->sharingDocumentId = null;
        $this->shareEmail = '';
    }

    public function confirmShare(): void
    {
        $this->validate(['shareEmail' => 'required|email']);

        $document = DmsDocument::query()->findOrFail($this->sharingDocumentId);

        $share = DmsShare::query()->create([
            'document_id' => $document->id,
            'grantee_type' => DmsShare::GRANTEE_EMAIL,
            'grantee_value' => $this->shareEmail,
            'ability' => $this->shareAbility,
            'expires_at' => now()->addDays((int) config('dms.share_link_expires_days')),
            'created_by' => auth()->id(),
        ]);

        NotificationFacade::route('mail', $this->shareEmail)
            ->notify(new DocumentSharedNotification($document, $share, auth()->user()));

        activity()->causedBy(auth()->user())->performedOn($document)->event('shared')
            ->withProperties(['to' => $this->shareEmail])
            ->log('Document shared by email');

        $this->cancelShare();
        $this->notifySuccess('Document shared.');
    }

    protected function notifySuccess(string $message): void
    {
        Notification::make()->title($message)->success()->send();
    }

    protected function notifyError(string $message): void
    {
        Notification::make()->title($message)->danger()->send();
    }
}
