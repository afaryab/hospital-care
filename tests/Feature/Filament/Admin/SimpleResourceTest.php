<?php

use App\Filament\Admin\Resources\Receptions\Pages\ManageReceptions;
use App\Filament\Admin\Resources\ServiceDepartments\Pages\ManageServiceDepartments;
use App\Filament\Admin\Resources\Services\Pages\ManageServices;
use App\Models\Administrator;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('receptions manage page renders', function () {
    Livewire\Livewire::test(ManageReceptions::class)->assertSuccessful();
});

test('service departments manage page renders', function () {
    Livewire\Livewire::test(ManageServiceDepartments::class)->assertSuccessful();
});

test('services manage page renders', function () {
    Livewire\Livewire::test(ManageServices::class)->assertSuccessful();
});
