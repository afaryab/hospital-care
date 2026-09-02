<?php

use App\Models\HospitalSetting;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('hospital name and logo url are shared with every Inertia page', function () {
    HospitalSetting::set('hospital_name', 'City Care Hospital');

    actingAs(User::factory()->create());

    get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('hospital.name', 'City Care Hospital')
            ->where('hospital.logoUrl', null)
        );
});

test('shared hospital name falls back to the app name when unset', function () {
    actingAs(User::factory()->create());

    get(route('home'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('hospital.name', config('app.name'))
        );
});
