<?php

use App\Models\Administrator;
use App\Models\User;

test('an admin without two-factor enabled is redirected to set it up by default', function () {
    $user = User::factory()->withoutTwoFactor()->create();
    Administrator::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertRedirect(route('two-factor.show'));
});

test('two-factor enforcement can be disabled via config', function () {
    config(['security.two_factor.enforced' => false]);

    $user = User::factory()->withoutTwoFactor()->create();
    Administrator::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertOk();
});

test('an admin with two-factor already enabled is never redirected', function () {
    $user = User::factory()->create();
    Administrator::factory()->create(['user_id' => $user->id]);

    $this->actingAs($user)
        ->get('/admin')
        ->assertOk();
});
