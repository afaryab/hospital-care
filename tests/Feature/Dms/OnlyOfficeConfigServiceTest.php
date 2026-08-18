<?php

use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use App\Services\OnlyOffice\OnlyOfficeConfigService;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
    config(['onlyoffice.jwt_secret' => 'test-secret']);
    $this->service = app(OnlyOfficeConfigService::class);
    $this->user = User::factory()->create();
    $folder = app(DmsFolderService::class)->create('Root', null, $this->user);
    $this->document = app(DmsDocumentService::class)->upload(
        UploadedFile::fake()->create('report.docx', 5),
        $folder,
        $this->user
    );
});

test('editorConfig builds a word-document config with a signed token', function () {
    $config = $this->service->editorConfig($this->document, $this->user);

    expect($config['document']['fileType'])->toBe('docx')
        ->and($config['documentType'])->toBe('word')
        ->and($config['document']['title'])->toBe($this->document->name)
        ->and($config['token'])->toBeString();

    $decoded = (array) JWT::decode($config['token'], new Key('test-secret', 'HS256'));
    expect($decoded['documentType'])->toBe('word');
});

test('documentKey changes when the document version changes', function () {
    $key1 = $this->service->documentKey($this->document);

    $this->document->update(['current_version' => 2]);
    $key2 = $this->service->documentKey($this->document->fresh());

    expect($key1)->not->toBe($key2);
});

test('documentType maps common extensions to the correct OnlyOffice type', function () {
    expect($this->service->documentType('xlsx'))->toBe('cell')
        ->and($this->service->documentType('pptx'))->toBe('slide')
        ->and($this->service->documentType('docx'))->toBe('word');
});

test('verifyToken accepts a valid Authorization bearer token and rejects a bad one', function () {
    $payload = ['foo' => 'bar'];
    $token = JWT::encode($payload, 'test-secret', 'HS256');

    $validRequest = Request::create('/callback', 'POST');
    $validRequest->headers->set('Authorization', "Bearer {$token}");
    expect($this->service->verifyToken($validRequest))->toBeTrue();

    $invalidRequest = Request::create('/callback', 'POST');
    $invalidRequest->headers->set('Authorization', 'Bearer not-a-real-token');
    expect($this->service->verifyToken($invalidRequest))->toBeFalse();
});
