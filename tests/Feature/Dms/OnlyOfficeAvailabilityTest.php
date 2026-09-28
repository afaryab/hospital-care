<?php

use App\Models\Administrator;
use App\Models\User;
use App\Services\Dms\DmsDocumentService;
use App\Services\Dms\DmsFolderService;
use App\Services\OnlyOffice\OnlyOfficeHealth;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

beforeEach(function () {
    Storage::fake('local');
    config(['onlyoffice.jwt_secret' => 'test-secret', 'onlyoffice.internal_url' => 'http://onlyoffice-documentserver']);
    app(OnlyOfficeHealth::class)->forget();

    $this->admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $this->admin->id]);
    actingAs($this->admin);

    $this->folder = app(DmsFolderService::class)->create('Reports', null, $this->admin);
    $this->document = app(DmsDocumentService::class)->upload(UploadedFile::fake()->create('report.docx', 5), $this->folder, $this->admin);
});

test('the document server counts as available when its healthcheck answers true', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('true')]);

    expect(app(OnlyOfficeHealth::class)->available())->toBeTrue();
});

test('an unreachable or unhealthy document server counts as unavailable', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => fn () => throw new ConnectionException('Connection refused')]);
    expect(app(OnlyOfficeHealth::class)->available())->toBeFalse();

    app(OnlyOfficeHealth::class)->forget();
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('false', 200)]);
    expect(app(OnlyOfficeHealth::class)->available())->toBeFalse();
});

test('the health result is cached so pages do not probe on every request', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('true')]);

    app(OnlyOfficeHealth::class)->available();
    app(OnlyOfficeHealth::class)->available();

    Http::assertSentCount(1);
});

test('ONLYOFFICE_ENABLED=false disables editing without contacting the server', function () {
    config(['onlyoffice.enabled' => false]);
    Http::fake();

    expect(app(OnlyOfficeHealth::class)->available())->toBeFalse();
    Http::assertNotSent(fn ($request) => str_contains($request->url(), '/healthcheck'));
});

test('opening the editor while the document server is down shows a notice instead of failing', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('', 502)]);

    get(route('onlyoffice.editor', $this->document))
        ->assertStatus(503)
        ->assertSee('The document editor is temporarily unavailable')
        ->assertSee('report.docx')
        ->assertDontSee('DocsAPI.DocEditor');
});

test('the editor opens normally when the document server is up', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('true')]);

    get(route('onlyoffice.editor', $this->document))
        ->assertOk()
        ->assertSee('DocsAPI.DocEditor', false);
});

test('the documents section is blocked with a notice while the document server is down', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('', 502)]);

    get(route('dms.index', $this->folder))
        ->assertStatus(503)
        ->assertInertia(fn (Assert $page) => $page->component('dms/unavailable'));

    get(route('dms.documents.download', $this->document))->assertStatus(503);

    $this->postJson(route('dms.folders.store'), ['name' => 'New'])->assertStatus(503);
});

test('the documents section works normally while the document server is up', function () {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response('true')]);

    get(route('dms.index', $this->folder))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page->component('dms/index')->has('documents', 1));
});

test('the shared documents flag hides the section from navigation while the server is down', function (string $body, bool $expected) {
    Http::fake(['onlyoffice-documentserver/healthcheck' => Http::response($body)]);

    get(route('home'))->assertInertia(fn (Assert $page) => $page->where('features.documents', $expected));
})->with([
    'server up' => ['true', true],
    'server down' => ['', false],
]);

test('non-admins never see documents and never trigger a health check', function () {
    Http::fake();
    actingAs(User::factory()->create());

    get(route('patients-register', ['all' => 1]))->assertInertia(fn (Assert $page) => $page->where('features.documents', false));

    Http::assertNotSent(fn ($request) => str_contains($request->url(), '/healthcheck'));
});
