<?php

use App\Filament\Admin\Widgets\AdminStatsOverview;
use App\Models\Administrator;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $admin = User::factory()->create();
    Administrator::create(['user_id' => $admin->id, 'authority' => 'administrator']);
    actingAs($admin);
});

test('all-time totals are cached rather than queried on every render', function () {
    Patient::factory()->count(3)->create();

    Livewire\Livewire::test(AdminStatsOverview::class)->assertSuccessful();

    expect(Cache::has('dashboard.admin.alltime_totals'))->toBeTrue();

    $cached = Cache::get('dashboard.admin.alltime_totals');
    expect($cached['total_patients'])->toBe(3);
});

test('a second render reuses the cached totals instead of re-querying', function () {
    Patient::factory()->count(2)->create();

    Livewire\Livewire::test(AdminStatsOverview::class)->assertSuccessful();

    Patient::factory()->create(); // would change the count if re-queried

    Livewire\Livewire::test(AdminStatsOverview::class)->assertSuccessful();

    $cached = Cache::get('dashboard.admin.alltime_totals');
    expect($cached['total_patients'])->toBe(2);
});
