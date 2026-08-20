<?php

use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('local');
    // JWT verification itself is covered by OnlyOfficeConfigServiceTest —
    // leaving the secret unset here exercises the "no secret configured"
    // degraded mode, so these tests can focus purely on the signed-URL and
    // status-code handling without also fabricating a JWT per request.
    config(['onlyoffice.jwt_secret' => null]);

    $this->user = User::factory()->create();
    $folder = app(DmsFolderService::class)->create('Root', null, $this->user);
    $this->document = app(DmsDocumentService::class)->upload(
        UploadedFile::fake()->create('report.docx', 5),
        $folder,
        $this->user
    );
});

test('callback rejects a request with no valid signature or token', function () {
    $response = post(route('onlyoffice.callback', $this->document), ['status' => 2]);

    $response->assertOk()->assertJson(['error' => 1]);
    expect($this->document->fresh()->current_version)->toBe(1);
});

test('callback with a valid signed url and status 2 saves a new version', function () {
    Http::fake([
        '*/download-payload*' => Http::response('new file contents', 200),
    ]);

    actingAs($this->user);

    $url = url()->temporarySignedRoute('onlyoffice.callback', now()->addMinutes(5), [
        'document' => $this->document->uuid,
        'actor' => $this->user->id,
    ]);

    $response = $this->postJson($url, [
        'status' => 2,
        'url' => 'https://example.com/download-payload',
    ]);

    $response->assertOk()->assertJson(['error' => 0]);
    expect($this->document->fresh()->current_version)->toBe(2);
});

test('callback with status 1 (still editing) does not create a new version', function () {
    $url = url()->temporarySignedRoute('onlyoffice.callback', now()->addMinutes(5), [
        'document' => $this->document->uuid,
        'actor' => $this->user->id,
    ]);

    $response = $this->postJson($url, ['status' => 1]);

    $response->assertOk()->assertJson(['error' => 0]);
    expect($this->document->fresh()->current_version)->toBe(1);
});
