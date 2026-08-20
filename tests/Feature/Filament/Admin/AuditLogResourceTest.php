<?php

use App\Filament\Admin\Resources\AuditLogs\Pages\ListAuditLogs;
use App\Models\Administrator;
use App\Models\ExpenseCategory;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

afterEach(function () {
    Activity::query()->delete();
});

test('audit log list page renders', function () {
    Livewire\Livewire::test(ListAuditLogs::class)->assertSuccessful();
});

test('the list table query count does not grow with the number of rows (no N+1)', function () {
    ExpenseCategory::factory()->create();

    DB::enableQueryLog();
    Livewire\Livewire::test(ListAuditLogs::class)->assertSuccessful();
    $queryCountForOneRow = count(DB::getQueryLog());
    DB::disableQueryLog();
    DB::flushQueryLog();

    ExpenseCategory::factory()->count(4)->create();

    DB::enableQueryLog();
    Livewire\Livewire::test(ListAuditLogs::class)->assertSuccessful();
    $queryCountForFiveRows = count(DB::getQueryLog());
    DB::disableQueryLog();

    // causer (a morphTo) is eager loaded via modifyQueryUsing() — without
    // it, each row's causer.name column would add one query per extra row,
    // swamping the small incidental variance (pagination/filter-option
    // queries) allowed here.
    expect($queryCountForFiveRows)->toBeLessThanOrEqual($queryCountForOneRow + 3);
});
