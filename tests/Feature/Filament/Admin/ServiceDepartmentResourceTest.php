<?php

use App\Enum\ServiceOrderTemplate;
use App\Filament\Admin\Resources\ServiceDepartments\Pages\ManageServiceDepartments;
use App\Filament\Admin\Resources\ServiceDepartments\ServiceDepartmentResource;
use App\Models\Administrator;
use App\Models\ServiceDepartment;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
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

test('service departments cannot be created from the panel', function () {
    expect(ServiceDepartmentResource::canCreate())->toBeFalse();

    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->assertActionDoesNotExist('create');
});

test('service departments cannot be deleted from the panel', function () {
    $department = ServiceDepartment::factory()->create();

    expect(ServiceDepartmentResource::canDelete($department))->toBeFalse()
        ->and(ServiceDepartmentResource::canDeleteAny())->toBeFalse();
});

test('admin can set a print template on an existing department without changing its slug', function () {
    $department = ServiceDepartment::factory()->create(['slug' => 'EMG', 'image' => '/img/emergency.png']);

    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->callAction(TestAction::make('edit')->table($department), data: [
            'name' => 'Emergency',
            'slug' => 'CHANGED',
            'have_composit_services' => 0,
            'service_order_template' => ServiceOrderTemplate::EmergencyTriageCompact->value,
        ])
        ->assertHasNoFormErrors();

    assertDatabaseHas(ServiceDepartment::class, [
        'id' => $department->id,
        'slug' => 'EMG',
        'image' => '/img/emergency.png',
        'service_order_template' => ServiceOrderTemplate::EmergencyTriageCompact->value,
    ]);
});

test('an image uploaded on edit resolves to a working public storage URL, not a bare filename', function () {
    $department = ServiceDepartment::factory()->create(['image' => '/img/xray.png']);

    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->callAction(TestAction::make('edit')->table($department), data: [
            'image' => UploadedFile::fake()->image('xray.jpg'),
        ])
        ->assertHasNoFormErrors();

    $department->refresh();

    // Filament's FileUpload saves a bare disk-relative path (no leading
    // slash); the accessor must turn it into a root-relative URL.
    expect($department->image)->not->toStartWith('http')
        ->and($department->image)->not->toStartWith('/img/')
        ->and($department->image_url)->toStartWith('/storage/service-departments/');
});

test('the service department table renders the resolved image_url, not a broken raw path', function () {
    $department = ServiceDepartment::factory()->create(['image' => '/img/emergency.png']);

    Livewire\Livewire::test(ManageServiceDepartments::class)
        ->assertTableColumnStateSet('image_url', $department->image_url, record: $department);
});
