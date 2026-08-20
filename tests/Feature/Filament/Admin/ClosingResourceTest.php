<?php

use App\Filament\Admin\Resources\Closings\Pages\CreateClosing;
use App\Filament\Admin\Resources\Closings\Pages\EditClosing;
use App\Filament\Admin\Resources\Closings\Pages\ListClosings;
use App\Filament\Admin\Resources\Closings\Pages\ViewClosing;
use App\Models\Administrator;
use App\Models\Closing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

test('the list table query count does not grow with the number of rows (no N+1)', function () {
    Closing::factory()->create();

    DB::enableQueryLog();
    Livewire\Livewire::test(ListClosings::class)->assertSuccessful();
    $queryCountForOneRow = count(DB::getQueryLog());
    DB::disableQueryLog();
    DB::flushQueryLog();

    Closing::factory()->count(4)->create();

    DB::enableQueryLog();
    Livewire\Livewire::test(ListClosings::class)->assertSuccessful();
    $queryCountForFiveRows = count(DB::getQueryLog());
    DB::disableQueryLog();

    // reception is eager loaded via modifyQueryUsing() — without it, each
    // row's formatStateUsing() closure accessing $record->reception would
    // add one query per extra row, swamping the small incidental variance
    // (pagination/filter-option queries) allowed here.
    expect($queryCountForFiveRows)->toBeLessThanOrEqual($queryCountForOneRow + 3);
});
