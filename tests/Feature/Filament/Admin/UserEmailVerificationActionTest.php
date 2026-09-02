<?php

use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Models\Administrator;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Livewire\Livewire;

beforeEach(function () {
    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);
    $this->actingAs($admin);
});

test('an admin can manually verify a user email', function () {
    $user = User::factory()->unverified()->create();

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('toggle_email_verification')->table($user))
        ->assertNotified();

    expect($user->fresh()->email_verified_at)->not->toBeNull();
});

test('an admin can manually unverify a user email', function () {
    $user = User::factory()->create();

    expect($user->email_verified_at)->not->toBeNull();

    Livewire::test(ListUsers::class)
        ->callAction(TestAction::make('toggle_email_verification')->table($user))
        ->assertNotified();

    expect($user->fresh()->email_verified_at)->toBeNull();
});
