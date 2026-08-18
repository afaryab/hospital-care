<?php

use App\Models\Administrator;
use App\Models\DmsFolder;
use App\Models\DmsShare;
use App\Models\OpdDoctor;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\User;
use App\Services\Dms\DmsFolderService;
use App\Services\Dms\DmsProvisioningService;
use Illuminate\Support\Facades\Gate;

beforeEach(function () {
    $this->folders = app(DmsFolderService::class);
    $this->provisioning = app(DmsProvisioningService::class);
    $this->owner = User::factory()->create();
    $this->stranger = User::factory()->create();
});

test('admin can view any folder, including one they did not create', function () {
    $admin = User::factory()->create();
    Administrator::factory()->create(['user_id' => $admin->id]);

    $folder = $this->folders->create('Root', null, $this->owner);

    expect(Gate::forUser($admin)->allows('view', $folder))->toBeTrue();
});

test('the creator can view their own folder', function () {
    $folder = $this->folders->create('Root', null, $this->owner);

    expect(Gate::forUser($this->owner)->allows('view', $folder))->toBeTrue();
});

test('a stranger cannot view a folder they have no relation to', function () {
    $folder = $this->folders->create('Root', null, $this->owner);

    expect(Gate::forUser($this->stranger)->allows('view', $folder))->toBeFalse();
});

test('a doctor can view their own system-linked doctor folder', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    $folder = $this->provisioning->doctorFolder($doctor);

    expect(Gate::forUser($doctor)->allows('view', $folder))->toBeTrue()
        ->and(Gate::forUser($this->stranger)->allows('view', $folder))->toBeFalse();
});

test('patient folder access mirrors PatientPolicy view access', function () {
    $patient = Patient::factory()->create();
    $folder = $this->provisioning->patientFolder($patient);

    $receptionist = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $receptionist->id]);

    expect(Gate::forUser($receptionist)->allows('view', $folder))->toBeTrue();
});

test('an active share grants access to a stranger', function () {
    $folder = $this->folders->create('Root', null, $this->owner);

    DmsShare::factory()->create([
        'folder_id' => $folder->id,
        'grantee_type' => DmsShare::GRANTEE_USER,
        'grantee_value' => (string) $this->stranger->id,
        'ability' => 'view',
        'created_by' => $this->owner->id,
    ]);

    expect(Gate::forUser($this->stranger)->allows('view', $folder))->toBeTrue();
});

test('an expired share does not grant access', function () {
    $folder = $this->folders->create('Root', null, $this->owner);

    DmsShare::factory()->create([
        'folder_id' => $folder->id,
        'grantee_type' => DmsShare::GRANTEE_USER,
        'grantee_value' => (string) $this->stranger->id,
        'ability' => 'view',
        'expires_at' => now()->subDay(),
        'created_by' => $this->owner->id,
    ]);

    expect(Gate::forUser($this->stranger)->allows('view', $folder))->toBeFalse();
});

test('a share on an ancestor folder is inherited by descendants', function () {
    $root = $this->folders->create('Root', null, $this->owner);
    $child = $this->folders->create('Child', $root, $this->owner);

    DmsShare::factory()->create([
        'folder_id' => $root->id,
        'grantee_type' => DmsShare::GRANTEE_USER,
        'grantee_value' => (string) $this->stranger->id,
        'ability' => 'view',
        'created_by' => $this->owner->id,
    ]);

    expect(Gate::forUser($this->stranger)->allows('view', $child))->toBeTrue();
});

test('system folders cannot be deleted or renamed even by their creator', function () {
    $folder = DmsFolder::factory()->system()->create(['created_by' => $this->owner->id]);

    expect(Gate::forUser($this->owner)->allows('update', $folder))->toBeFalse()
        ->and(Gate::forUser($this->owner)->allows('delete', $folder))->toBeFalse();
});
