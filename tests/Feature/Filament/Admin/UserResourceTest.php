<?php

use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use App\Filament\Admin\Resources\Users\Pages\ViewUser;
use App\Models\Administrator;
use App\Models\User;

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
