<?php

use App\Enum\ServiceOrderTemplate;
use App\Filament\Admin\Resources\ServiceDepartments\Pages\ManageServiceDepartments;
use App\Models\Administrator;
use App\Models\ServiceDepartment;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;

beforeEach(function () {
    Storage::fake('public');
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('service department manage page renders', function () {
    Livewire\Livewire::test(ManageServiceDepartments::class)->assertSuccessful();
});

test('service department manage page shows the configured print template', function () {
    $departments = ServiceDepartment::factory()->count(2)->create();

    Livewire\Livewire::test(ManageServiceDepartments::class)->assertCanSeeTableRecords($departments);
});

test('admin can create a service department with a print template', function () {
    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->callAction('create', data: [
            'name' => 'Emergency',
            'slug' => 'EMG-2',
            'image' => UploadedFile::fake()->image('emergency.jpg'),
            'have_composit_services' => 0,
            'service_order_template' => ServiceOrderTemplate::EmergencyTriageCompact->value,
        ])
        ->assertNotified();

    assertDatabaseHas(ServiceDepartment::class, [
        'name' => 'Emergency',
        'service_order_template' => ServiceOrderTemplate::EmergencyTriageCompact->value,
    ]);
});

test('service department print template is left unset when not chosen', function () {
    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->callAction('create', data: [
            'name' => 'Ultrasound',
            'slug' => 'ULT-2',
            'image' => UploadedFile::fake()->image('ultrasound.jpg'),
            'have_composit_services' => 0,
        ])
        ->assertNotified();

    assertDatabaseHas(ServiceDepartment::class, [
        'name' => 'Ultrasound',
        'service_order_template' => null,
    ]);
});

test('the uploaded image resolves to a working public storage URL, not a bare filename', function () {
    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->callAction('create', data: [
            'name' => 'Radiology',
            'slug' => 'RAD-2',
            'image' => UploadedFile::fake()->image('xray.jpg'),
            'have_composit_services' => 0,
        ])
        ->assertNotified();

    $department = ServiceDepartment::where('name', 'Radiology')->firstOrFail();

    // Filament's FileUpload saves a bare disk-relative path (e.g.
    // "service-departments/01AB....jpg", no leading slash) — that's exactly
    // what broke <img src> before this fix, since the browser resolves it
    // relative to the current page URL instead of the site root. The
    // accessor must turn it into a root-relative or absolute URL that
    // actually points at the file.
    expect($department->image)->not->toStartWith('http')
        ->and($department->image)->not->toStartWith('/img/')
        ->and($department->image_url)->toStartWith('/storage/service-departments/');
});

test('the service department table renders the resolved image_url, not a broken raw path', function () {
    $department = ServiceDepartment::factory()->create(['image' => '/img/emergency.png']);

    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->assertTableColumnStateSet('image_url', $department->image_url, record: $department);
});
