<?php

use App\Models\Administrator;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('the api throttle limiter is actually registered and enforced', function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    actingAs($admin);

    // 120/minute per user — send one more than that and expect a 429 on
    // the last request. A cheap, side-effect-free endpoint to hammer.
    for ($i = 0; $i < 120; $i++) {
        $this->getJson('/api/lookup')->assertStatus(200);
    }

    $this->getJson('/api/lookup')->assertStatus(429);
});
