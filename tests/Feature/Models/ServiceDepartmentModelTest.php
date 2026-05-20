<?php

use App\Models\ServiceDepartment;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
