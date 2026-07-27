<?php

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Pages\ViewUser;
use App\Models\Administrator;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('user list page renders', function () {
    Livewire\Livewire::test(ListUsers::class)->assertSuccessful();
});

test('user create page renders', function () {
    Livewire\Livewire::test(CreateUser::class)->assertSuccessful();
});

test('user view page renders', function () {
    $user = User::factory()->create();
    Livewire\Livewire::test(ViewUser::class, ['record' => $user->getRouteKey()])->assertSuccessful();
});

test('user edit page renders', function () {
    $user = User::factory()->create();
    Livewire\Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])->assertSuccessful();
});

test('a password can be set when creating a user', function () {
    Livewire\Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Password User',
            'email' => 'password-user@example.com',
            'password' => 'super-secret',
            'login_attempts' => 0,
            'is_active' => true,
            'patientManagerProfiles' => [],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $created = User::where('email', 'password-user@example.com')->first();
    expect($created)->not->toBeNull();
    expect(Hash::check('super-secret', $created->password))->toBeTrue();
});

test('editing a user without a password keeps the existing one', function () {
    $user = User::factory()->create();
    $originalHash = $user->fresh()->password;

    Livewire\Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['password' => ''])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($user->fresh()->password)->toBe($originalHash);
});

test('editing a user with a new password updates it', function () {
    $user = User::factory()->create();

    Livewire\Livewire::test(EditUser::class, ['record' => $user->getRouteKey()])
        ->fillForm(['password' => 'a-new-password'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(Hash::check('a-new-password', $user->fresh()->password))->toBeTrue();
});

test('an emergency doctor profile requires a PMDC number', function () {
    Livewire\Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Dr No Pmdc',
            'email' => 'no-pmdc@example.com',
            'password' => 'super-secret',
            'login_attempts' => 0,
            'is_active' => true,
            'emergencyDoctorProfiles' => [
                ['authority' => 'assistant', 'pmdc_number' => ''],
            ],
        ])
        ->call('create')
        ->assertHasFormErrors(['emergencyDoctorProfiles.0.pmdc_number' => 'required']);
});

test('an emergency doctor profile persists authority and PMDC number', function () {
    Livewire\Livewire::test(CreateUser::class)
        ->fillForm([
            'name' => 'Dr With Pmdc',
            'email' => 'with-pmdc@example.com',
            'password' => 'super-secret',
            'login_attempts' => 0,
            'is_active' => true,
            'emergencyDoctorProfiles' => [
                ['authority' => 'consultant', 'pmdc_number' => '99887-P'],
            ],
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $created = User::where('email', 'with-pmdc@example.com')->first();
    $profile = $created->emergencyDoctorProfiles()->first();
    expect($profile->authority)->toBe('consultant');
    expect($profile->pmdc_number)->toBe('99887-P');
});
