<?php

use App\Models\EmergencyDoctor;
use App\Models\NursingStaff;
use App\Models\ServiceOrder;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('emg dashboard is not restricted to today — older open orders still show, latest first', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $older = ServiceOrder::factory()->create([
        'type' => 'EMG', 'doctor_id' => $doctor->id, 'status' => 'open', 'created_at' => now()->subDays(3),
    ]);
    $newer = ServiceOrder::factory()->create([
        'type' => 'EMG', 'doctor_id' => $doctor->id, 'status' => 'open', 'created_at' => now(),
    ]);

    $this->get('/EMG')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('emg/index')
            ->has('recentOrders', 2)
            ->where('recentOrders.0.id', $newer->id)
            ->where('recentOrders.1.id', $older->id)
            ->where('isEmgDoctor', true)
            ->where('isDoctorScoped', true)
        );
});

test('nursing staff have EMG access but see the unscoped queue and cannot discharge', function () {
    $nurse = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $nurse->id]);
    $this->actingAs($nurse);

    $otherDoctor = User::factory()->create();
    $order = ServiceOrder::factory()->create([
        'type' => 'EMG', 'doctor_id' => $otherDoctor->id, 'status' => 'open',
    ]);

    $this->get('/EMG')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('emg/index')
            ->where('isEmgDoctor', true)
            ->where('isDoctorScoped', false)
            ->has('recentOrders', 1)
            ->where('recentOrders.0.id', $order->id)
        );

    $this->get("/EMG/{$order->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('emg/patient')
            ->where('canDischarge', false)
        );
});

test('a user with no EMG-related profile has no access', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/EMG')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('emg/index')
            ->where('isEmgDoctor', false)
            ->has('recentOrders', 0)
        );
});

test('an emergency doctor can discharge', function () {
    $doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor);

    $order = ServiceOrder::factory()->create(['type' => 'EMG', 'doctor_id' => $doctor->id]);

    $this->get("/EMG/{$order->id}")
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('emg/patient')
            ->where('canDischarge', true)
        );
});
