<?php

use App\Filament\Admin\Resources\AuditLogs\AuditLogResource;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

afterEach(function () {
    Activity::query()->delete();
});

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);
});

test('creating a patient logs an activity entry', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $patient = Patient::factory()->create([
        'cnic' => '35202-3333333-1',
        'contact' => '03221234567',
        'address' => 'Audit Street',
    ]);

    $activity = Activity::query()
        ->where('subject_type', Patient::class)
        ->where('subject_id', $patient->id)
        ->where('event', 'created')
        ->first();

    expect($activity)->not->toBeNull();
});

test('updating a transaction logs old and new values', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $transaction = Transaction::factory()->create([
        'amount' => 1000,
    ]);

    $transaction->update(['amount' => 1500]);

    $activity = Activity::query()
        ->where('subject_type', Transaction::class)
        ->where('subject_id', $transaction->id)
        ->where('event', 'updated')
        ->latest('id')
        ->first();

    $changesJson = (string) DB::table('activity_log')->where('id', $activity?->id)->value('attribute_changes');

    expect($activity)->not->toBeNull()
        ->and($changesJson)->toContain('amount');
});

test('deleting an expense voucher logs the deletion', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $voucher = ExpenseVoucher::factory()->create();

    $voucherId = $voucher->id;
    $voucher->delete();

    $activity = Activity::query()
        ->where('subject_type', ExpenseVoucher::class)
        ->where('subject_id', $voucherId)
        ->where('event', 'deleted')
        ->first();

    expect($activity)->not->toBeNull();
});

test('activity log captures authenticated user', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    $patient = Patient::factory()->create();

    $activity = Activity::query()
        ->where('subject_type', Patient::class)
        ->where('subject_id', $patient->id)
        ->where('event', 'created')
        ->first();

    expect($activity?->causer_id)->toBe($user->id)
        ->and($activity?->causer_type)->toBe(User::class);
});

test('activity log captures ip address and user agent metadata keys', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Patient::factory()->create();

    $activity = Activity::query()->where('subject_type', Patient::class)->latest('id')->first();
    $properties = json_decode((string) DB::table('activity_log')->where('id', $activity?->id)->value('properties'), true);

    expect((array) $properties)->toHaveKey('ip_address')
        ->and((array) $properties)->toHaveKey('user_agent');
});

test('audit log resource registers an index page', function () {
    expect(AuditLogResource::getPages())->toHaveKey('index');
});

test('audit log resource is read only', function () {
    expect(AuditLogResource::canCreate())->toBeFalse();
});
