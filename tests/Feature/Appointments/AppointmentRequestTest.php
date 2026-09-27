<?php

use App\Enum\AppointmentRequestStatus;
use App\Models\Appointment;
use App\Models\AppointmentRequest;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\post;
use function Pest\Laravel\postJson;

function bookableService(): Service
{
    return Service::factory()->create(['generate_service_order' => true, 'is_composit_service' => false]);
}

function validRequestPayload(array $overrides = []): array
{
    return [
        'name' => 'Sana Malik',
        'contact' => '0300 1234567',
        'gender' => 'f',
        'age_years' => 29,
        'service_id' => bookableService()->id,
        'preferred_date' => now()->addDays(3)->toDateString(),
        'preferred_time' => 'morning',
        'notes' => 'Follow-up',
        ...$overrides,
    ];
}

function receptionistActor(): User
{
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    return $user;
}

test('the public booking page is available without logging in', function () {
    $service = bookableService();

    get(route('public-appointments.create'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('public/book-appointment')
            ->where('serviceGroups.0.services.0.id', $service->id)
        );
});

test('a member of the public can submit a request, stored with an encrypted phone number', function () {
    $response = post(route('public-appointments.store'), validRequestPayload());

    $request = AppointmentRequest::query()->sole();
    $response->assertRedirect(route('public-appointments.submitted', ['reference' => $request->reference]));

    expect($request->status)->toBe(AppointmentRequestStatus::Pending)
        ->and($request->contact)->toBe('0300 1234567')
        ->and(DB::table('appointment_requests')->value('contact'))->not->toContain('1234567')
        ->and(Patient::query()->count())->toBe(0);

    get(route('public-appointments.submitted', ['reference' => $request->reference]))->assertOk();
});

test('invalid and spam submissions are rejected', function () {
    post(route('public-appointments.store'), validRequestPayload(['preferred_date' => now()->subDay()->toDateString()]))
        ->assertSessionHasErrors('preferred_date');

    post(route('public-appointments.store'), validRequestPayload(['contact' => '12']))
        ->assertSessionHasErrors('contact');

    post(route('public-appointments.store'), validRequestPayload(['website' => 'http://spam.example']))
        ->assertSessionHasErrors('website');

    expect(AppointmentRequest::query()->count())->toBe(0);
});

test('public submissions are rate limited per IP', function () {
    foreach (range(1, 5) as $i) {
        post(route('public-appointments.store'), validRequestPayload())->assertRedirect();
    }

    post(route('public-appointments.store'), validRequestPayload())->assertTooManyRequests();
});

test('the public API lists services, accepts a request and reports only its status', function () {
    $service = bookableService();

    getJson(route('api-v1-public.services'))->assertOk()->assertJsonPath('data.0.services.0.id', $service->id);

    $reference = postJson(route('api-v1-public.appointment-requests.store'), validRequestPayload(['service_id' => $service->id]))
        ->assertCreated()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonMissingPath('data.name')
        ->assertJsonMissingPath('data.contact')
        ->json('data.reference');

    getJson(route('api-v1-public.appointment-requests.show', $reference))
        ->assertOk()
        ->assertJsonPath('data.status', 'pending')
        ->assertJsonMissingPath('data.contact');
});

test('reception sees pending requests with patients matching the phone number', function () {
    receptionistActor();
    $existing = Patient::factory()->create(['contact' => '03001234567']);
    AppointmentRequest::factory()->create(['contact' => '0300-1234567']);

    get(route('appointment-requests'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('appointments/requests')
            ->where('pendingCount', 1)
            ->where('requests.data.0.matching_patients.0.id', $existing->id)
        );
});

test('confirming a request with a new patient registers them and books the appointment', function () {
    $user = receptionistActor();
    $request = AppointmentRequest::factory()->create(['name' => 'New Person', 'age_years' => 40]);
    $scheduledAt = now()->addDays(2)->setTime(10, 0);

    post(route('appointment-requests.confirm', $request), [
        'patient_id' => null,
        'service_id' => $request->service_id,
        'scheduled_at' => $scheduledAt->format('Y-m-d H:i'),
    ])->assertRedirect()->assertSessionHasNoErrors();

    $request->refresh();
    $patient = Patient::query()->where('name', 'New Person')->sole();

    expect($request->status)->toBe(AppointmentRequestStatus::Confirmed)
        ->and($request->patient_id)->toBe($patient->id)
        ->and($request->handled_by)->toBe($user->id)
        ->and($patient->age)->toBe(40)
        ->and(Appointment::query()->find($request->appointment_id))
        ->patient_id->toBe($patient->id)
        ->service_id->toBe($request->service_id);
});

test('confirming against an existing patient does not create a duplicate', function () {
    receptionistActor();
    $existing = Patient::factory()->create();
    $request = AppointmentRequest::factory()->create();

    post(route('appointment-requests.confirm', $request), [
        'patient_id' => $existing->id,
        'service_id' => $request->service_id,
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasNoErrors();

    expect(Patient::query()->count())->toBe(1)
        ->and($request->fresh()->patient_id)->toBe($existing->id);
});

test('a request can only be handled once, and can be rejected with a reason', function () {
    receptionistActor();
    $request = AppointmentRequest::factory()->create();

    post(route('appointment-requests.reject', $request), ['rejection_reason' => 'Unreachable'])->assertSessionHasNoErrors();

    expect($request->fresh())->status->toBe(AppointmentRequestStatus::Rejected)
        ->rejection_reason->toBe('Unreachable');

    post(route('appointment-requests.confirm', $request), [
        'service_id' => $request->service_id,
        'scheduled_at' => now()->addDay()->format('Y-m-d H:i'),
    ])->assertSessionHasErrors('request');
});

test('staff without reception access cannot see or handle requests', function () {
    actingAs(User::factory()->create());
    $request = AppointmentRequest::factory()->create();

    get(route('appointment-requests'))->assertForbidden();
    post(route('appointment-requests.reject', $request), ['rejection_reason' => 'x'])->assertForbidden();
});
