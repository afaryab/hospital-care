<?php

use App\Enum\AppointmentPriorityMode;
use App\Enum\AppointmentStatus;
use App\Models\Appointment;
use App\Models\Closing;
use App\Models\HospitalSetting;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\User;
use App\Services\AppointmentService;
use Illuminate\Support\Facades\DB;

function createOpdService(): Service
{
    $department = ServiceDepartment::factory()->create(['slug' => 'OPD']);

    return Service::factory()->create([
        'service_department_id' => $department->id,
        'charges' => 500,
    ]);
}

test('booking a priority appointment creates a draft receivable but no service order yet', function () {
    HospitalSetting::set('appointment_priority_mode', AppointmentPriorityMode::Priority->value);

    $service = createOpdService();
    $patient = Patient::factory()->create();
    $creator = User::factory()->create();

    $appointment = app(AppointmentService::class)->book([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDays(3),
        'created_by' => $creator->id,
    ]);

    expect($appointment->priority_mode)->toBe(AppointmentPriorityMode::Priority);
    expect($appointment->service_order_id)->toBeNull();
    expect($appointment->receaveable_id)->not->toBeNull();

    $receaveable = Receaveable::find($appointment->receaveable_id);
    expect($receaveable->status)->toBe('draft');
    expect((float) $receaveable->amount)->toBe(500.0);
});

test('booking a standard appointment creates no draft receivable', function () {
    HospitalSetting::set('appointment_priority_mode', AppointmentPriorityMode::Standard->value);

    $service = createOpdService();
    $patient = Patient::factory()->create();
    $creator = User::factory()->create();

    $appointment = app(AppointmentService::class)->book([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDay(),
        'created_by' => $creator->id,
    ]);

    expect($appointment->priority_mode)->toBe(AppointmentPriorityMode::Standard);
    expect($appointment->receaveable_id)->toBeNull();
    expect($appointment->service_order_id)->toBeNull();
});

test('materialize command only reserves a service order for todays priority/medium appointments', function () {
    $service = createOpdService();

    $priorityToday = Appointment::factory()->priority()->scheduledToday()->create(['service_id' => $service->id]);
    $mediumToday = Appointment::factory()->medium()->scheduledToday()->create(['service_id' => $service->id]);
    $standardToday = Appointment::factory()->scheduledToday()->create(['service_id' => $service->id]);
    $priorityFuture = Appointment::factory()->priority()->create([
        'service_id' => $service->id,
        'scheduled_at' => now()->addWeek(),
    ]);

    $this->artisan('app:materialize-appointments')->assertSuccessful();

    $priorityToday->refresh();
    $mediumToday->refresh();
    $standardToday->refresh();
    $priorityFuture->refresh();

    expect($priorityToday->service_order_id)->not->toBeNull();
    expect($priorityToday->serviceOrder->status)->toBe('reserved');
    expect((int) $priorityToday->serviceOrder->priority)->toBe(1);

    expect($mediumToday->service_order_id)->not->toBeNull();
    expect((int) $mediumToday->serviceOrder->priority)->toBe(0);

    expect($standardToday->service_order_id)->toBeNull();
    expect($priorityFuture->service_order_id)->toBeNull();
});

test('a materialized priority reservation is ordered ahead of an earlier walk-in in the opd queue', function () {
    $service = createOpdService();
    $user = User::factory()->create();

    $walkIn = ServiceOrder::factory()->create([
        'type' => 'OPD',
        'service_id' => $service->id,
        'status' => 'open',
        'created_at' => now()->subHour(),
    ]);

    $appointment = Appointment::factory()->priority()->scheduledToday()->create(['service_id' => $service->id]);
    $this->artisan('app:materialize-appointments')->assertSuccessful();
    $appointment->refresh();

    $this->actingAs($user);
    $response = $this->get(route('hospital-opd-queue'));
    $response->assertOk();

    $orders = $response->original->getData()['page']['props']['serviceOrdersByService'][$service->id];
    $ids = collect($orders)->pluck('id')->toArray();

    expect(array_search($appointment->service_order_id, $ids))->toBeLessThan(array_search($walkIn->id, $ids));
});

test('medium appointment reservations expose priority_mode so the frontend can mask identity', function () {
    $service = createOpdService();
    $user = User::factory()->create();

    $appointment = Appointment::factory()->medium()->scheduledToday()->create(['service_id' => $service->id]);
    $this->artisan('app:materialize-appointments')->assertSuccessful();
    $appointment->refresh();

    $this->actingAs($user);
    $response = $this->get(route('hospital-opd-queue'));
    $response->assertOk();

    $orders = $response->original->getData()['page']['props']['serviceOrdersByService'][$service->id];
    $reserved = collect($orders)->firstWhere('id', $appointment->service_order_id);

    expect($reserved['status'])->toBe('reserved');
    expect($reserved['appointment']['priority_mode'])->toBe('medium');
});

test('checking in a priority appointment settles the draft receivable and opens the reserved service order', function () {
    HospitalSetting::set('appointment_priority_mode', AppointmentPriorityMode::Priority->value);

    $service = createOpdService();
    $patient = Patient::factory()->create();
    $user = User::factory()->create();
    Closing::factory()->create(['receptionist_id' => $user->id, 'status' => 'open']);

    $appointment = app(AppointmentService::class)->book([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now(),
        'created_by' => $user->id,
    ]);

    $this->artisan('app:materialize-appointments')->assertSuccessful();
    $appointment->refresh();

    $reservedOrderId = $appointment->service_order_id;
    $ordersBefore = ServiceOrder::count();

    $this->actingAs($user);
    $response = $this->post(route('transaction-store'), [
        'income_or_expense' => 'INCOME',
        'patient_id' => $patient->id,
        'department_key' => 'OPD',
        'appointment_id' => $appointment->id,
        'total_amount' => 500,
        'payment_method' => 'CASH',
        'amount_paid' => 500,
        'items' => [
            ['service_id' => $service->id, 'quantity' => 1, 'total' => 500],
        ],
    ]);

    $response->assertRedirect();

    expect(ServiceOrder::count())->toBe($ordersBefore); // no duplicate order created

    $appointment->refresh();
    expect($appointment->status)->toBe(AppointmentStatus::CheckedIn);
    expect($appointment->checked_in_at)->not->toBeNull();

    $reservedOrder = ServiceOrder::find($reservedOrderId);
    expect($reservedOrder->status)->toBe('open');

    $receaveable = Receaveable::find($appointment->receaveable_id);
    expect($receaveable->status)->toBe('paid');
});

test('expiring a no-show priority appointment cancels its draft receivable and closes its reserved slot', function () {
    HospitalSetting::set('appointment_priority_mode', AppointmentPriorityMode::Priority->value);

    $service = createOpdService();
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $appointment = app(AppointmentService::class)->book([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now(),
        'created_by' => $user->id,
    ]);

    $this->artisan('app:materialize-appointments')->assertSuccessful();
    $appointment->refresh();

    $this->artisan('app:expire-no-show-appointments')->assertSuccessful();

    $appointment->refresh();
    expect($appointment->status)->toBe(AppointmentStatus::NoShow);

    $receaveable = Receaveable::find($appointment->receaveable_id);
    expect($receaveable->status)->toBe('cancelled');

    $serviceOrder = ServiceOrder::find($appointment->service_order_id);
    expect($serviceOrder->status)->toBe('CLOSED');
    expect($serviceOrder->closed_at)->not->toBeNull();
});

test('cancelling a scheduled appointment cancels its draft receivable', function () {
    HospitalSetting::set('appointment_priority_mode', AppointmentPriorityMode::Priority->value);

    $service = createOpdService();
    $patient = Patient::factory()->create();
    $user = User::factory()->create();

    $appointment = app(AppointmentService::class)->book([
        'patient_id' => $patient->id,
        'service_id' => $service->id,
        'scheduled_at' => now()->addDays(2),
        'created_by' => $user->id,
    ]);

    $this->actingAs($user);
    $response = $this->post(route('appointment-cancel', ['appointment' => $appointment->id]));
    $response->assertRedirect();

    $appointment->refresh();
    expect($appointment->status)->toBe(AppointmentStatus::Cancelled);

    $receaveable = Receaveable::find($appointment->receaveable_id);
    expect($receaveable->status)->toBe('cancelled');
});

test('a draft receivable is excluded from the generic receivables PDF report', function () {
    $patient = Patient::factory()->create();

    Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'amount' => 1000,
        'orignal_amount' => 1000,
        'status' => 'draft',
    ]);

    Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'amount' => 200,
        'orignal_amount' => 200,
        'status' => 'unpaid',
    ]);

    $user = User::factory()->create();
    $this->actingAs($user);

    DB::enableQueryLog();

    $response = $this->get('/reports/generic/receivables?from='.now()->subDay()->toDateString().'&until='.now()->addDay()->toDateString());
    $response->assertOk();

    $receaveableQuery = collect(DB::getQueryLog())
        ->first(fn ($entry) => str_contains($entry['query'], 'receaveables') && str_contains($entry['query'], 'select'));

    DB::disableQueryLog();

    expect($receaveableQuery['query'])->toContain('"status" != ?');
    expect($receaveableQuery['bindings'])->toContain('draft');
});

test('a draft receivable is excluded from the financial KPI outstanding receivables stat', function () {
    $patient = Patient::factory()->create();

    Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'amount' => 5000,
        'orignal_amount' => 5000,
        'status' => 'draft',
    ]);

    Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'amount' => 300,
        'orignal_amount' => 300,
        'status' => 'unpaid',
    ]);

    $outstanding = Receaveable::whereNotIn('status', ['paid', 'cancelled', 'draft'])->sum('amount');

    expect((float) $outstanding)->toBe(300.0);
});
