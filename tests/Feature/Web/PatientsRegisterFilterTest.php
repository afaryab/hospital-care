<?php

use App\Helpers\DateHelper;
use App\Models\Patient;
use App\Models\ServiceOrder;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('register search matches by patient name', function () {
    actingAs(User::factory()->create());

    $match = Patient::factory()->create(['name' => 'Ayesha Khan']);
    $other = Patient::factory()->create(['name' => 'Bilal Ahmed']);

    get(route('patients-register', ['search' => 'Ayesha']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('patientsPaginated.data', 1)
            ->where('patientsPaginated.data.0.id', $match->id)
        );
});

test('register search matches by ps_number', function () {
    actingAs(User::factory()->create());

    $match = Patient::factory()->create(['ps_number' => 'PS/2026/01/0099']);
    Patient::factory()->create(['ps_number' => 'PS/2026/01/0001']);

    get(route('patients-register', ['search' => '0099']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('patientsPaginated.data', 1)
            ->where('patientsPaginated.data.0.id', $match->id)
        );
});

test('register search matches by service order so_number and so_short', function () {
    actingAs(User::factory()->create());

    $match = Patient::factory()->create();
    ServiceOrder::factory()->create([
        'patient_id' => $match->id,
        'so_number' => 'PS/2026/01/0001/OPD/01',
        'so_short' => '99887766',
    ]);
    $other = Patient::factory()->create();
    ServiceOrder::factory()->create([
        'patient_id' => $other->id,
        'so_number' => 'PS/2026/01/0002/OPD/01',
        'so_short' => '11223344',
    ]);

    get(route('patients-register', ['search' => '99887766']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('patientsPaginated.data', 1)
            ->where('patientsPaginated.data.0.id', $match->id)
        );
});

test('register contact filter matches by patient contact', function () {
    actingAs(User::factory()->create());

    // Contact is encrypted at rest, so lookups match on the full normalized
    // number via a deterministic hash rather than a partial/substring search.
    $match = Patient::factory()->create(['contact' => '03001234567']);
    Patient::factory()->create(['contact' => '03007654321']);

    get(route('patients-register', ['contact' => '0300-1234567']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('patientsPaginated.data', 1)
            ->where('patientsPaginated.data.0.id', $match->id)
        );
});

test('register defaults to the current year and month', function () {
    actingAs(User::factory()->create());

    $now = now(DateHelper::timezone());

    get(route('patients-register'))
        ->assertRedirect(route('patients-register-year-month', ['year' => $now->format('Y'), 'month' => $now->format('m')]));
});

test('register can still list every period when all is requested', function () {
    actingAs(User::factory()->create());

    $old = Patient::factory()->create(['created_at' => now()->subYears(2)]);
    $recent = Patient::factory()->create();

    get(route('patients-register', ['all' => 1]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('yearSelected', '0')
            ->where('monthSelected', '0')
            ->has('patientsPaginated.data', 2)
            ->where('patientsPaginated.data.0.id', $recent->id)
            ->where('patientsPaginated.data.1.id', $old->id)
        );
});

test('register lists the selected month newest first with a stable tie-breaker', function () {
    actingAs(User::factory()->create());

    $sameMoment = now()->startOfMonth()->addDay();
    $first = Patient::factory()->create(['created_at' => $sameMoment]);
    $second = Patient::factory()->create(['created_at' => $sameMoment]);
    $latest = Patient::factory()->create(['created_at' => $sameMoment->copy()->addHour()]);

    get(route('patients-register-year-month', ['year' => $sameMoment->format('Y'), 'month' => $sameMoment->format('m')]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('monthSelected', $sameMoment->format('m'))
            ->where('patientsPaginated.data.0.id', $latest->id)
            ->where('patientsPaginated.data.1.id', $second->id)
            ->where('patientsPaginated.data.2.id', $first->id)
        );
});
