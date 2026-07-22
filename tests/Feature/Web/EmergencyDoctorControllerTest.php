<?php

use App\Models\EmergencyDoctor;
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
        );
});
