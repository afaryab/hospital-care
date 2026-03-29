<?php

use App\Filament\Admin\Resources\Closings\Pages\CreateClosing;
use App\Filament\Admin\Resources\Closings\Pages\EditClosing;
use App\Filament\Admin\Resources\Closings\Pages\ListClosings;
use App\Filament\Admin\Resources\Closings\Pages\ViewClosing;
use App\Models\Administrator;
use App\Models\Closing;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('closing list page renders', function () {
    Livewire\Livewire::test(ListClosings::class)->assertSuccessful();
});

test('closing create page renders', function () {
    Livewire\Livewire::test(CreateClosing::class)->assertSuccessful();
});

test('closing view page renders', function () {
    $closing = Closing::factory()->create();
    Livewire\Livewire::test(ViewClosing::class, ['record' => $closing->getRouteKey()])->assertSuccessful();
});

test('closing edit page renders', function () {
    $closing = Closing::factory()->create();
    Livewire\Livewire::test(EditClosing::class, ['record' => $closing->getRouteKey()])->assertSuccessful();
});
