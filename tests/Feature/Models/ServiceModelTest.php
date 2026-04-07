<?php

use App\Models\Service;

test('service can be created with factory', function () {
    $service = Service::factory()->create();

    $this->assertDatabaseHas('services', ['id' => $service->id]);
});

test('service belongs to department', function () {
    $service = Service::factory()->create();

    expect($service->department())->toBeInstanceOf(Illuminate\Database\Eloquent\Relations\BelongsTo::class)
        ->and($service->department)->not->toBeNull();
});

test('service casts boolean fields correctly', function () {
    $service = Service::factory()->create([
        'have_service_provider' => true,
        'is_featured' => true,
        'generate_service_order' => false,
    ]);

    expect($service->have_service_provider)->toBeTrue()
        ->and($service->is_featured)->toBeTrue()
        ->and($service->generate_service_order)->toBeFalse();
});

test('service casts service_provider_types to json', function () {
    $types = ['App\\Models\\OpdDoctor'];
    $service = Service::factory()->create(['service_provider_types' => $types]);

    expect($service->service_provider_types)->toBe($types);
});

test('service can store a health icon name', function () {
    $service = Service::factory()->create([
        'icon' => 'o-doctor-male',
    ]);

    expect($service->icon)->toBe('o-doctor-male');
});
