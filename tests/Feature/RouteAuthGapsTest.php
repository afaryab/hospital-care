<?php

use App\Models\Receptionist;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

// ─── /import-old ────────────────────────────────────────────────────────────

test('guests cannot reach the legacy import route', function () {
    get(route('import-old'))->assertRedirect(route('login'));
});

test('a non-admin authenticated user cannot trigger the legacy import route', function () {
    $receptionist = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $receptionist->id]);
    actingAs($receptionist);

    get(route('import-old'))->assertForbidden();
});

// ─── routes/settings.php — verified middleware ─────────────────────────────

test('an unverified user is redirected away from settings/profile', function () {
    $user = User::factory()->unverified()->create();
    actingAs($user);

    get(route('profile.edit'))->assertRedirect(route('verification.notice'));
});

test('an unverified user is redirected away from settings/password', function () {
    $user = User::factory()->unverified()->create();
    actingAs($user);

    get(route('user-password.edit'))->assertRedirect(route('verification.notice'));
});

test('an unverified user is redirected away from settings/two-factor', function () {
    $user = User::factory()->unverified()->create();
    actingAs($user);

    get(route('two-factor.show'))->assertRedirect(route('verification.notice'));
});

test('a verified user can still reach settings/profile', function () {
    $user = User::factory()->create();
    actingAs($user);

    get(route('profile.edit'))->assertOk();
});
