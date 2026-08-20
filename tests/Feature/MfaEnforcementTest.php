<?php

use App\Models\Accountant;
use App\Models\Administrator;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

test('an admin without two-factor enabled is redirected away from the admin panel', function () {
    $admin = User::factory()->withoutTwoFactor()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    actingAs($admin);

    get('/admin')->assertRedirect(route('two-factor.show'));
});

test('an admin with two-factor enabled can reach the admin panel', function () {
    $admin = User::factory()->create(); // factory default has 2FA confirmed
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    actingAs($admin);

    get('/admin')->assertOk();
});

test('an accountant without two-factor enabled is redirected away from the accounts panel', function () {
    $accountant = User::factory()->withoutTwoFactor()->create();
    Accountant::create(['user_id' => $accountant->id, 'authority' => 'assistant']);
    actingAs($accountant);

    get('/accounts')->assertRedirect(route('two-factor.show'));
});

test('a two-factor-less admin can still log out of the admin panel rather than being trapped', function () {
    $admin = User::factory()->withoutTwoFactor()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    actingAs($admin);

    post('/admin/logout')->assertRedirect();

    $this->assertGuest();
});
