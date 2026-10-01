<?php

use App\Enum\SlipPhotoSource;
use App\Enum\SlipPhotoSubject;
use App\Models\Closing;
use App\Models\OpdDoctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\SlipPhoto;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\TestResponse;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

beforeEach(function () {
    Storage::fake('local');
});

/**
 * @return array{0: User, 1: Closing}
 */
function receptionistWithOpenCounter(): array
{
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    $closing = Closing::factory()->create(['receptionist_id' => $user->id, 'status' => 'open']);

    return [$user, $closing];
}

/**
 * @return array{year: int|string, month: int|string, number: int|string}
 */
function psRouteParams(Patient $patient): array
{
    return ['year' => $patient->year, 'month' => $patient->month, 'number' => $patient->number];
}

function postSlipPhoto(Patient $patient, array $overrides = []): TestResponse
{
    return test()->postJson(route('slip-photo-store', psRouteParams($patient)), array_merge([
        'photo' => UploadedFile::fake()->image('slip.jpg', 640, 480),
        'subject' => 'patient',
        'source' => 'auto',
    ], $overrides));
}

function postIncomeSlip(Patient $patient): TestResponse
{
    $department = ServiceDepartment::query()->firstOrCreate(['slug' => 'OPD'], ServiceDepartment::factory()->raw(['slug' => 'OPD']));
    $service = Service::factory()->create(['service_department_id' => $department->id, 'charges' => 500]);

    return test()->post(route('transaction-store'), [
        'income_or_expense' => 'INCOME',
        'patient_id' => $patient->id,
        'department_key' => 'OPD',
        'total_amount' => 500,
        'payment_method' => 'CASH',
        'amount_paid' => 500,
        'items' => [
            ['service_id' => $service->id, 'quantity' => 1, 'total' => 500],
        ],
    ]);
}

test('a receptionist with an open counter can capture a pending slip photo', function () {
    [$user, $closing] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();

    $this->actingAs($user);
    $response = postSlipPhoto($patient);

    $response->assertCreated()
        ->assertJsonPath('data.subject', 'patient')
        ->assertJsonPath('data.source', 'auto');

    $slipPhoto = SlipPhoto::sole();
    expect($slipPhoto->closing_id)->toBe($closing->id)
        ->and($slipPhoto->patient_id)->toBe($patient->id)
        ->and($slipPhoto->transaction_id)->toBeNull()
        ->and($slipPhoto->captured_by)->toBe($user->id)
        ->and($slipPhoto->photo()?->disk)->toBe('local');

    expect(Activity::query()->where('event', 'slip_photo_captured')->where('subject_id', $patient->id)->exists())->toBeTrue();
});

test('a retake replaces the pending photo and keeps the old one soft-deleted', function () {
    [$user] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);

    postSlipPhoto($patient)->assertCreated();
    postSlipPhoto($patient, ['subject' => 'guardian', 'source' => 'manual'])->assertCreated();

    expect(SlipPhoto::query()->whereNull('transaction_id')->count())->toBe(1)
        ->and(SlipPhoto::sole()->subject)->toBe(SlipPhotoSubject::Guardian)
        ->and(SlipPhoto::withTrashed()->count())->toBe(2);
});

test('generating an income slip attaches the pending photo, and each slip needs its own', function () {
    [$user] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);

    postSlipPhoto($patient)->assertCreated();
    postIncomeSlip($patient)->assertRedirect();

    $firstSlip = Transaction::query()->latest('id')->first();
    expect($firstSlip->slipPhoto)->not->toBeNull()
        ->and($firstSlip->slipPhoto->subject)->toBe(SlipPhotoSubject::Patient);

    postIncomeSlip($patient)->assertRedirect();

    $secondSlip = Transaction::query()->latest('id')->first();
    expect($secondSlip->id)->not->toBe($firstSlip->id)
        ->and($secondSlip->slipPhoto)->toBeNull();
});

test('a slip is still generated when no photo was captured', function () {
    [$user] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);

    postIncomeSlip($patient)->assertRedirect();

    expect(Transaction::query()->where('patient_id', $patient->id)->count())->toBe(1)
        ->and(SlipPhoto::count())->toBe(0);
});

test('a photo pending at one counter is not attached to a slip at another counter', function () {
    [$userA] = receptionistWithOpenCounter();
    [$userB] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();

    $this->actingAs($userA);
    postSlipPhoto($patient)->assertCreated();

    $this->actingAs($userB);
    postIncomeSlip($patient)->assertRedirect();

    expect(Transaction::query()->latest('id')->first()->slipPhoto)->toBeNull()
        ->and(SlipPhoto::sole()->transaction_id)->toBeNull();
});

function postUnassignedSlipPhoto(array $overrides = []): TestResponse
{
    return test()->postJson(route('slip-photo-store-unassigned'), array_merge([
        'photo' => UploadedFile::fake()->image('slip.jpg', 640, 480),
        'subject' => 'patient',
        'source' => 'manual',
    ], $overrides));
}

function visitCounterPatient(Patient $patient): TestResponse
{
    return test()->get(route('counter-select-department', ['pYear' => $patient->year, 'pMonth' => $patient->month, 'number' => $patient->number]));
}

test('a photo snapped before choosing a patient goes to the patient selected next', function () {
    [$user, $closing] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);

    $id = postUnassignedSlipPhoto(['subject' => 'guardian'])->assertCreated()->json('data.id');
    expect(SlipPhoto::find($id)->patient_id)->toBeNull();

    $this->get(route('counter-select-patient'))
        ->assertInertia(fn (Assert $page) => $page->where('pendingSlipPhoto.id', $id));

    visitCounterPatient($patient)
        ->assertInertia(fn (Assert $page) => $page->where('pendingSlipPhoto.id', $id)->where('pendingSlipPhoto.subject', 'guardian'));

    expect(SlipPhoto::find($id)->patient_id)->toBe($patient->id);
    expect(Activity::query()->where('event', 'slip_photo_assigned')->where('subject_id', $patient->id)->exists())->toBeTrue();

    postIncomeSlip($patient)->assertRedirect();
    expect(Transaction::query()->latest('id')->first()->slipPhoto?->id)->toBe($id);
});

test('an unassigned snap is not claimed when stale, from another counter, or the patient already has one', function () {
    [$user, $closing] = receptionistWithOpenCounter();
    [$otherUser] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();

    $this->actingAs($otherUser);
    $otherCounterId = postUnassignedSlipPhoto()->json('data.id');

    $this->actingAs($user);
    $staleId = postUnassignedSlipPhoto()->json('data.id');
    SlipPhoto::whereKey($staleId)->update(['captured_at' => now()->subMinutes(SlipPhoto::CLAIM_WINDOW_MINUTES + 1)]);

    visitCounterPatient($patient)->assertInertia(fn (Assert $page) => $page->where('pendingSlipPhoto', null));
    expect(SlipPhoto::find($staleId)->patient_id)->toBeNull()
        ->and(SlipPhoto::find($otherCounterId)->patient_id)->toBeNull();

    $ownId = postSlipPhoto($patient)->json('data.id');
    $freshUnassignedId = postUnassignedSlipPhoto()->json('data.id');

    visitCounterPatient($patient)->assertInertia(fn (Assert $page) => $page->where('pendingSlipPhoto.id', $ownId));
    expect(SlipPhoto::find($freshUnassignedId)->patient_id)->toBeNull();
});

test('retaking before choosing a patient replaces the earlier unassigned snap', function () {
    [$user] = receptionistWithOpenCounter();
    $this->actingAs($user);

    $firstId = postUnassignedSlipPhoto()->json('data.id');
    $secondId = postUnassignedSlipPhoto()->json('data.id');

    expect(SlipPhoto::find($firstId))->toBeNull()
        ->and(SlipPhoto::withTrashed()->find($firstId)->trashed())->toBeTrue()
        ->and(SlipPhoto::find($secondId))->not->toBeNull();
});

test('capturing requires an open counter', function () {
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    $patient = Patient::factory()->create();

    $this->actingAs($user);
    postSlipPhoto($patient)->assertStatus(409);

    expect(SlipPhoto::count())->toBe(0);
});

test('a user who cannot create slips cannot capture slip photos', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $patient = Patient::factory()->create();

    $this->actingAs($doctor);
    postSlipPhoto($patient)->assertForbidden();
});

test('capture validates the image and the subject', function () {
    [$user] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);

    postSlipPhoto($patient, ['photo' => UploadedFile::fake()->create('slip.pdf', 10, 'application/pdf'), 'subject' => 'neighbour'])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['photo', 'subject']);
});

test('a pending photo can be relabelled, but not once it is on a slip', function () {
    [$user] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);

    $id = postSlipPhoto($patient)->json('data.id');

    $this->patchJson(route('slip-photo-update', $id), ['subject' => 'guardian'])
        ->assertOk()
        ->assertJsonPath('data.subject', 'guardian');

    postIncomeSlip($patient)->assertRedirect();

    $this->patchJson(route('slip-photo-update', $id), ['subject' => 'patient'])->assertForbidden();
    expect(SlipPhoto::find($id)->subject)->toBe(SlipPhotoSubject::Guardian);
});

test('slip photos are served only to the capturer, slip viewers and admins', function () {
    [$capturer] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($capturer);
    $id = postSlipPhoto($patient)->json('data.id');

    $this->get(route('slip-photo-show', $id))->assertOk();

    [$otherReceptionist] = receptionistWithOpenCounter();
    $this->actingAs($otherReceptionist)->get(route('slip-photo-show', $id))->assertForbidden();

    $this->actingAs($capturer);
    postIncomeSlip($patient)->assertRedirect();

    $this->actingAs($otherReceptionist)->get(route('slip-photo-show', $id))->assertOk();

    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $this->actingAs($doctor)->get(route('slip-photo-show', $id))->assertForbidden();

    auth()->logout();
    $this->get(route('slip-photo-show', $id))->assertRedirect(route('login'));
});

test('the counter income page shares the pending slip photo', function () {
    [$user] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);
    $id = postSlipPhoto($patient, ['subject' => 'guardian'])->json('data.id');

    $this->get(route('counter-select-department', ['pYear' => $patient->year, 'pMonth' => $patient->month, 'number' => $patient->number]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('counter/income')
            ->where('pendingSlipPhoto.id', $id)
            ->where('pendingSlipPhoto.subject', 'guardian'));
});

test('the counter and transaction views include the slip photo', function () {
    [$user, $closing] = receptionistWithOpenCounter();
    $patient = Patient::factory()->create();
    $this->actingAs($user);
    $id = postSlipPhoto($patient)->json('data.id');
    postIncomeSlip($patient)->assertRedirect();
    $transaction = Transaction::query()->latest('id')->first();

    [, $ctYear, $ctMonth, $ctNumber] = explode('/', $closing->ct_number);
    $this->get(route('counter-view', compact('ctYear', 'ctMonth', 'ctNumber')))
        ->assertInertia(fn (Assert $page) => $page
            ->component('counter/view')
            ->where("slipPhotos.{$transaction->id}.id", $id));

    [, $tYear, $tMonth, $tDay, $tNumber] = explode('/', $transaction->tr_number);
    $this->get(route('transaction-view', compact('tYear', 'tMonth', 'tDay', 'tNumber')))
        ->assertInertia(fn (Assert $page) => $page
            ->component('transaction/view')
            ->where('slipPhoto.id', $id)
            ->where('slipPhoto.source', SlipPhotoSource::Auto->value));
});
