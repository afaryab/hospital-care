<?php

use App\Models\EmergencyDoctor;
use App\Models\IndDoctor;
use App\Models\OpdDoctor;
use App\Models\ServiceOrder;
use App\Models\User;

test('ServiceOrder::latestSoShortPrefix returns the latest so_short minus its last digit', function () {
    ServiceOrder::factory()->create(['type' => 'EMG', 'so_short' => 'EMG/00001333']);
    ServiceOrder::factory()->create(['type' => 'EMG', 'so_short' => 'EMG/00001334']);

    expect(ServiceOrder::latestSoShortPrefix(['EMG'], 'EMG'))->toBe('EMG/0000133');
});

test('ServiceOrder::latestSoShortPrefix falls back to a zero prefix when the department has no orders yet', function () {
    expect(ServiceOrder::latestSoShortPrefix(['ULT'], 'ULT'))->toBe('ULT/0000000');
});

test('opd search by so_short prefix matches partial results', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $match = ServiceOrder::factory()->create(['type' => 'OPD', 'so_short' => 'OPD/00001334']);
    ServiceOrder::factory()->create(['type' => 'OPD', 'so_short' => 'OPD/00009999']);

    $response = $this->postJson('/api/opd/search', ['q' => 'OPD/000013']);

    $response->assertOk();
    $ids = collect($response->json('data.possible'))->pluck('id');
    expect($ids)->toContain($match->id);
});

test('ind search by so_short prefix matches partial results', function () {
    $doctor = User::factory()->create();
    IndDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $match = ServiceOrder::factory()->create(['type' => 'IND', 'so_short' => 'IND/00000042']);
    ServiceOrder::factory()->create(['type' => 'IND', 'so_short' => 'IND/00009999']);

    $response = $this->postJson('/api/ind/search', ['q' => 'IND/000000']);

    $response->assertOk();
    $ids = collect($response->json('data.possible'))->pluck('id');
    expect($ids)->toContain($match->id);
});

test('department search by dept-prefixed digits term matches so_short', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $match = ServiceOrder::factory()->create(['type' => 'EMG', 'so_short' => 'EMG/00001334']);
    ServiceOrder::factory()->create(['type' => 'EMG', 'so_short' => 'EMG/00009999']);

    $response = $this->postJson('/api/emg/search', ['q' => 'EMG/0000133', 'types' => ['EMG']]);

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id');
    expect($ids)->toContain($match->id)->toHaveCount(1);
});

test('opd dashboard passes a searchPrefill prop derived from the latest so_short', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    ServiceOrder::factory()->create(['type' => 'OPD', 'so_short' => 'OPD/00001334']);

    $response = $this->get(route('opd-dashboard'));

    $response->assertOk();
    expect($response->original->getData()['page']['props']['searchPrefill'])->toBe('OPD/0000133');
});
