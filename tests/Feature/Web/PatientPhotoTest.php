<?php

use App\Models\Accountant;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Activitylog\Models\Activity;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

beforeEach(function () {
    Storage::fake('local');
    $this->patient = Patient::factory()->create();
    [, $year, $month, $number] = explode('/', $this->patient->ps_number);
    $this->params = ['year' => $year, 'month' => $month, 'number' => $number];
});

function receptionistUser(): User
{
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);

    return $user;
}

test('a receptionist can save a webcam capture as the patient photo', function () {
    actingAs($user = receptionistUser());

    post(route('patient-photo-store', $this->params), [
        'photo' => UploadedFile::fake()->image('capture.jpg', 640, 480),
        'source' => 'camera',
    ])->assertRedirect()->assertSessionHasNoErrors();

    $photo = $this->patient->fresh()->currentPhoto();

    expect($photo)->not->toBeNull()
        ->and($photo->disk)->toBe('local')
        ->and($photo->getCustomProperty('source'))->toBe('camera')
        ->and($photo->getCustomProperty('uploaded_by'))->toBe($user->id);

    Storage::disk('local')->assertExists($photo->getPathRelativeToRoot());

    expect(Activity::query()->where('event', 'photo_updated')->where('subject_id', $this->patient->id)->exists())->toBeTrue();
});

test('a new photo becomes current while earlier photos are kept', function () {
    actingAs(receptionistUser());

    post(route('patient-photo-store', $this->params), ['photo' => UploadedFile::fake()->image('a.jpg'), 'source' => 'upload']);
    post(route('patient-photo-store', $this->params), ['photo' => UploadedFile::fake()->image('b.png'), 'source' => 'upload']);

    $patient = $this->patient->fresh();

    expect($patient->getMedia(Patient::PHOTOS_COLLECTION))->toHaveCount(2)
        ->and($patient->currentPhoto()->mime_type)->toBe('image/png');
});

test('a user without patient update rights cannot change the photo', function () {
    $accountant = User::factory()->create();
    Accountant::factory()->create(['user_id' => $accountant->id]);
    actingAs($accountant);

    post(route('patient-photo-store', $this->params), [
        'photo' => UploadedFile::fake()->image('capture.jpg'),
        'source' => 'camera',
    ])->assertForbidden();

    expect($this->patient->fresh()->currentPhoto())->toBeNull();
});

test('non-image files are rejected', function () {
    actingAs(receptionistUser());

    post(route('patient-photo-store', $this->params), [
        'photo' => UploadedFile::fake()->create('notes.pdf', 10, 'application/pdf'),
        'source' => 'upload',
    ])->assertSessionHasErrors('photo');
});

test('the photo is served only to users who may view the patient', function () {
    actingAs(receptionistUser());
    post(route('patient-photo-store', $this->params), ['photo' => UploadedFile::fake()->image('a.jpg'), 'source' => 'upload']);

    get(route('patient-photo-show', $this->params))->assertOk()->assertHeader('Content-Type', 'image/jpeg');

    actingAs(User::factory()->create());
    get(route('patient-photo-show', $this->params))->assertForbidden();
});

test('the patient page exposes the photo url and whether it can be changed', function () {
    actingAs(receptionistUser());

    get(route('patients-register-ps-number', $this->params))
        ->assertInertia(fn (Assert $page) => $page
            ->where('patientPhotoUrl', null)
            ->where('canUpdatePatient', true)
        );

    post(route('patient-photo-store', $this->params), ['photo' => UploadedFile::fake()->image('a.jpg'), 'source' => 'camera']);

    get(route('patients-register-ps-number', $this->params))
        ->assertInertia(fn (Assert $page) => $page
            ->where('patientPhotoUrl', fn ($url) => str_starts_with($url, route('patient-photo-show', $this->params, false)))
        );
});
