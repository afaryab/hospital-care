<?php

use App\Models\Incident;
use App\Models\Patient;
use App\Models\User;
use App\Notifications\SecurityIncidentNotification;
use App\Services\BreachDetectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    config()->set('security.breach.notification_emails', ['security@example.com']);
    config()->set('security.breach.failed_login_threshold', 5);
    config()->set('security.breach.failed_login_window_minutes', 10);
    config()->set('security.breach.bulk_patient_access_threshold', 5);
    config()->set('security.breach.bulk_patient_window_minutes', 5);
});

test('multiple failed login attempts triggers alert', function () {
    Notification::fake();

    $service = app(BreachDetectionService::class);

    $request = Request::create('/login', 'POST', ['email' => 'unknown@example.com'], [], [], [
        'REMOTE_ADDR' => '127.0.0.1',
        'HTTP_USER_AGENT' => 'PestAgent/1.0',
    ]);

    foreach (range(1, 5) as $attempt) {
        $service->recordFailedLogin($request, null, 'unknown@example.com');
    }

    expect(Incident::query()->where('type', 'failed_login_threshold')->exists())->toBeTrue();

    Notification::assertSentOnDemand(SecurityIncidentNotification::class);
});

test('login from new ip triggers notification', function () {
    Notification::fake();

    $user = User::factory()->create([
        'ip_address' => '10.0.0.10',
    ]);

    $service = app(BreachDetectionService::class);

    $initialLoginRequest = Request::create('/login', 'POST', [], [], [], [
        'REMOTE_ADDR' => '10.0.0.10',
        'HTTP_USER_AGENT' => 'KnownDevice/1.0',
    ]);

    $service->recordSuccessfulLogin($user, $initialLoginRequest);

    $newIpRequest = Request::create('/login', 'POST', [], [], [], [
        'REMOTE_ADDR' => '10.0.0.11',
        'HTTP_USER_AGENT' => 'KnownDevice/1.0',
    ]);

    $service->recordSuccessfulLogin($user->fresh(), $newIpRequest);

    expect(Incident::query()->where('type', 'new_login_context')->exists())->toBeTrue();

    Notification::assertSentOnDemand(SecurityIncidentNotification::class);
});

test('bulk patient record access triggers alert', function () {
    Notification::fake();

    $user = User::factory()->create();
    $patient = Patient::factory()->create();
    $service = app(BreachDetectionService::class);

    $request = Request::create('/PS/2026/03/0001', 'GET', [], [], [], [
        'REMOTE_ADDR' => '192.168.1.25',
        'HTTP_USER_AGENT' => 'ReceptionDesk/1.0',
    ]);

    foreach (range(1, 5) as $count) {
        $service->recordPatientAccess($user, $patient, $request);
    }

    expect(Incident::query()->where('type', 'bulk_patient_access')->exists())->toBeTrue();

    Notification::assertSentOnDemand(SecurityIncidentNotification::class);
});

test('incident log entry is created for each alert', function () {
    Notification::fake();

    $user = User::factory()->create(['ip_address' => '10.0.0.20']);
    $patient = Patient::factory()->create();
    $service = app(BreachDetectionService::class);

    $failedLoginRequest = Request::create('/login', 'POST', ['email' => 'attack@example.com'], [], [], [
        'REMOTE_ADDR' => '172.16.0.5',
        'HTTP_USER_AGENT' => 'AttackAgent/1.0',
    ]);

    foreach (range(1, 5) as $count) {
        $service->recordFailedLogin($failedLoginRequest, null, 'attack@example.com');
    }

    $newIpRequest = Request::create('/login', 'POST', [], [], [], [
        'REMOTE_ADDR' => '10.0.0.21',
        'HTTP_USER_AGENT' => 'KnownDevice/1.0',
    ]);
    $service->recordSuccessfulLogin($user, $newIpRequest);

    $bulkAccessRequest = Request::create('/PS/2026/03/0002', 'GET', [], [], [], [
        'REMOTE_ADDR' => '172.16.0.15',
        'HTTP_USER_AGENT' => 'DeskClient/2.0',
    ]);
    foreach (range(1, 5) as $count) {
        $service->recordPatientAccess($user, $patient, $bulkAccessRequest);
    }

    expect(Incident::query()->whereIn('type', [
        'failed_login_threshold',
        'new_login_context',
        'bulk_patient_access',
    ])->count())->toBe(3);
});

test('security contacts receive email notification', function () {
    Notification::fake();

    config()->set('security.breach.notification_emails', [
        'security1@example.com',
        'security2@example.com',
    ]);
    config()->set('security.breach.failed_login_threshold', 1);

    $service = app(BreachDetectionService::class);
    $request = Request::create('/login', 'POST', ['email' => 'threat@example.com'], [], [], [
        'REMOTE_ADDR' => '203.0.113.5',
        'HTTP_USER_AGENT' => 'ThreatAgent/3.0',
    ]);

    $service->recordFailedLogin($request, null, 'threat@example.com');

    Notification::assertSentOnDemandTimes(SecurityIncidentNotification::class, 2);
});
