<?php

use App\Models\User;

// --- Guests are redirected ---

test('guests are redirected to login when accessing home', function () {
    $this->get(route('home'))->assertRedirect(route('login'));
});

test('guests are redirected to login when accessing all patients list', function () {
    $this->get(route('patients-register'))->assertRedirect(route('login'));
});

test('guests are redirected to login when accessing receivables', function () {
    $this->get(route('receaveables'))->assertRedirect(route('login'));
});

test('guests are redirected to login when accessing counter list', function () {
    $this->get(route('my-counter-list'))->assertRedirect(route('login'));
});

test('guests are redirected to login when accessing counter open form', function () {
    $this->get(route('counter-open'))->assertRedirect(route('login'));
});

// --- Authenticated users can access routes ---

test('authenticated user can access home route', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('home'))
        ->assertOk();
});

test('authenticated user can access all patients list', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('patients-register'))
        ->assertOk();
});

test('authenticated user can access receivables page', function () {
    $user = User::factory()->create();
    \App\Models\Closing::factory()->create(['receptionist_id' => $user->id, 'status' => 'open']);

    $this->actingAs($user)
        ->get(route('receaveables'))
        ->assertOk();
});

test('authenticated user can access counter list page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('my-counter-list'))
        ->assertOk();
});

test('authenticated user can access counter open form', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('counter-open'))
        ->assertOk();
});
