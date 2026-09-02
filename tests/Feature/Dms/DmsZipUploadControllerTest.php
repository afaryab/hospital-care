<?php

use App\Models\Administrator;
use App\Models\DmsDocument;
use App\Models\User;
use App\Services\Dms\DmsFolderService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('local');
    config(['dms.tmp_path' => 'dms-tmp-upload-controller-test']);

    $this->admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $this->admin->id]);
    actingAs($this->admin);

    $this->folder = app(DmsFolderService::class)->create('Reports', null, $this->admin);
});

function dmsUploadControllerTestZip(array $entries): string
{
    $path = tempnam(sys_get_temp_dir(), 'dmsuploadctrl').'.zip';
    $zip = new ZipArchive;
    $zip->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

    foreach ($entries as $name => $contents) {
        $zip->addFromString($name, $contents);
    }

    $zip->close();

    return $path;
}

test('a safe zip is extracted into the target folder as documents', function () {
    $path = dmsUploadControllerTestZip(['a.txt' => 'hello', 'b.txt' => 'world']);

    post(route('dms.zip-uploads.store'), [
        'file' => new UploadedFile($path, 'safe.zip', 'application/zip', null, true),
        'folder_uuid' => $this->folder->uuid,
    ])->assertRedirect();

    expect(DmsDocument::query()->where('folder_id', $this->folder->id)->count())->toBe(2);

    @unlink($path);
});

test('a zip with path-traversal entries is rejected', function () {
    $path = dmsUploadControllerTestZip(['../../etc/passwd' => 'evil']);

    post(route('dms.zip-uploads.store'), [
        'file' => new UploadedFile($path, 'slip.zip', 'application/zip', null, true),
        'folder_uuid' => $this->folder->uuid,
    ])->assertSessionHasErrors();

    expect(DmsDocument::query()->where('folder_id', $this->folder->id)->count())->toBe(0);

    @unlink($path);
});
