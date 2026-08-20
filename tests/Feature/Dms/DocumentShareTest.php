<?php

use App\Filament\Admin\Pages\DocumentManager;
use App\Models\Administrator;
use App\Models\DmsShare;
use App\Models\User;
use App\Notifications\DocumentSharedNotification;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

use function Pest\Laravel\get;

beforeEach(function () {
    Storage::fake('local');
    Notification::fake();

    $this->admin = User::factory()->create();
    Administrator::create(['user_id' => $this->admin->id, 'authority' => 'administrator']);
    $this->actingAs($this->admin);

    $folder = app(DmsFolderService::class)->create('Root', null, $this->admin);
    $this->document = app(DmsDocumentService::class)->upload(
        UploadedFile::fake()->create('a.pdf', 5),
        $folder,
        $this->admin
    );
});

test('sharing a document from the explorer creates a share and notifies the recipient', function () {
    Livewire::test(DocumentManager::class)
        ->call('startShare', $this->document->id)
        ->set('shareEmail', 'someone@example.com')
        ->call('confirmShare')
        ->assertSuccessful();

    expect(DmsShare::query()->where('document_id', $this->document->id)->count())->toBe(1);

    Notification::assertSentOnDemand(DocumentSharedNotification::class);
});

test('a valid signed share link downloads the document', function () {
    $share = DmsShare::query()->create([
        'document_id' => $this->document->id,
        'grantee_type' => DmsShare::GRANTEE_EMAIL,
        'grantee_value' => 'someone@example.com',
        'ability' => 'view',
        'expires_at' => now()->addDay(),
        'created_by' => $this->admin->id,
    ]);

    $url = url()->temporarySignedRoute('dms.shares.download', now()->addDay(), ['share' => $share->id]);

    get($url)->assertOk();

    expect($share->fresh()->accessed_at)->not->toBeNull();
});

test('an expired share link is rejected', function () {
    $share = DmsShare::query()->create([
        'document_id' => $this->document->id,
        'grantee_type' => DmsShare::GRANTEE_EMAIL,
        'grantee_value' => 'someone@example.com',
        'ability' => 'view',
        'expires_at' => now()->subDay(),
        'created_by' => $this->admin->id,
    ]);

    $url = url()->temporarySignedRoute('dms.shares.download', now()->addDay(), ['share' => $share->id]);

    get($url)->assertForbidden();
});
