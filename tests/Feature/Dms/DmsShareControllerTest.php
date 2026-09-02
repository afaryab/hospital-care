<?php

use App\Models\Administrator;
use App\Models\DmsShare;
use App\Models\User;
use App\Notifications\DocumentSharedNotification;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('local');
    Notification::fake();

    $this->admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $this->admin->id]);
    actingAs($this->admin);

    $folder = app(DmsFolderService::class)->create('Reports', null, $this->admin);
    $this->document = app(DmsDocumentService::class)->upload(
        UploadedFile::fake()->create('a.pdf', 5),
        $folder,
        $this->admin
    );
});

test('sharing a document creates a share and notifies the recipient by email', function () {
    post(route('dms.documents.share', $this->document), [
        'email' => 'someone@example.com',
        'ability' => 'view',
    ])->assertRedirect();

    expect(DmsShare::query()->where('document_id', $this->document->id)->where('grantee_value', 'someone@example.com')->count())->toBe(1);

    Notification::assertSentOnDemand(DocumentSharedNotification::class);
});

test('sharing requires a valid email and ability', function () {
    post(route('dms.documents.share', $this->document), [
        'email' => 'not-an-email',
        'ability' => 'destroy',
    ])->assertSessionHasErrors(['email', 'ability']);
});
