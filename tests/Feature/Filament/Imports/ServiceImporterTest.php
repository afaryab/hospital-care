<?php

use App\Filament\Imports\ServiceImporter;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\User;

test('importing a service resolves its department by name and creates a slug', function () {
    $this->actingAs(User::factory()->create());
    $department = ServiceDepartment::factory()->create(['name' => 'Laboratory']);

    $import = makeFilamentImport(ServiceImporter::class);
    $importer = new ServiceImporter($import, [
        'name' => 'name',
        'department' => 'department',
        'charges' => 'charges',
    ], []);

    $importer([
        'name' => 'Complete Blood Count',
        'department' => 'Laboratory',
        'charges' => '500',
    ]);

    $service = Service::where('name', 'Complete Blood Count')->first();
    expect($service)->not->toBeNull()
        ->and($service->service_department_id)->toBe($department->id)
        ->and((float) $service->charges)->toBe(500.0)
        ->and($service->slug)->not->toBeNull();
});

test('re-importing an existing service by name updates it without duplicating', function () {
    $this->actingAs(User::factory()->create());
    $department = ServiceDepartment::factory()->create(['name' => 'Laboratory']);
    Service::factory()->create(['name' => 'Complete Blood Count', 'service_department_id' => $department->id, 'charges' => 400]);

    $import = makeFilamentImport(ServiceImporter::class);
    $importer = new ServiceImporter($import, [
        'name' => 'name',
        'department' => 'department',
        'charges' => 'charges',
    ], []);

    $importer([
        'name' => 'Complete Blood Count',
        'department' => 'Laboratory',
        'charges' => '600',
    ]);

    expect(Service::where('name', 'Complete Blood Count')->count())->toBe(1)
        ->and((float) Service::where('name', 'Complete Blood Count')->value('charges'))->toBe(600.0);
});
