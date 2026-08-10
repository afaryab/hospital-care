<?php

use App\Enum\ServiceOrderTemplate;
use App\Models\ServiceDepartment;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

test('service department can be created with factory', function () {
    $department = ServiceDepartment::factory()->create();

    $this->assertDatabaseHas('service_departments', ['id' => $department->id]);
});

test('service department has many services', function () {
    $department = ServiceDepartment::factory()->create();

    expect($department->services())->toBeInstanceOf(HasMany::class);
});

test('service department casts have_composit_services to boolean', function () {
    $department = ServiceDepartment::factory()->create(['have_composit_services' => true]);

    expect($department->have_composit_services)->toBeTrue();
});

test('service department has no print template configured by default', function () {
    $department = ServiceDepartment::factory()->create();

    expect($department->service_order_template)->toBeNull();
});

test('service department casts service_order_template to the enum', function () {
    $department = ServiceDepartment::factory()->create([
        'service_order_template' => ServiceOrderTemplate::EmergencyTriageCompact,
    ]);

    expect($department->fresh()->service_order_template)->toBe(ServiceOrderTemplate::EmergencyTriageCompact);
});

test('image_url passes through a seeded /img/ public-folder path as an absolute URL', function () {
    $department = ServiceDepartment::factory()->create(['image' => '/img/emergency.png']);

    expect($department->image_url)->toBe(asset('/img/emergency.png'));
});

test('image_url passes through a full http(s) URL unchanged', function () {
    $department = ServiceDepartment::factory()->create(['image' => 'https://cdn.example.com/logo.png']);

    expect($department->image_url)->toBe('https://cdn.example.com/logo.png');
});

test('image_url resolves a Filament FileUpload storage-disk path (no leading slash) to a public storage URL', function () {
    // This is exactly what Filament's FileUpload saves after an edit — a bare
    // disk-relative path with no leading slash and no "storage/" prefix.
    $department = ServiceDepartment::factory()->create(['image' => 'service-departments/01ABCDEF.png']);

    expect($department->image_url)->toBe(Storage::disk('public')->url('service-departments/01ABCDEF.png'))
        ->and($department->image_url)->toContain('/storage/service-departments/01ABCDEF.png');
});

test('image_url is null when no image is set', function () {
    $department = ServiceDepartment::factory()->create(['image' => '']);

    expect($department->image_url)->toBeNull();
});

test('image_url is appended when the model is serialized', function () {
    $department = ServiceDepartment::factory()->create(['image' => '/img/emergency.png']);

    expect($department->toArray())->toHaveKey('image_url');
});
