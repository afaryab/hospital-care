<?php

use App\Models\OpdDoctor;
use App\Models\PedDoctor;
use App\Models\Receptionist;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;
use Database\Seeders\ServicesAndDepartmentsSeeder;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

function pedsDoctor(): User
{
    $doctor = User::factory()->create();
    PedDoctor::factory()->create(['user_id' => $doctor->id]);

    return $doctor;
}

test('the seeder creates the Peds department with the Paeds shift services', function () {
    $this->seed(ServicesAndDepartmentsSeeder::class);

    $ped = ServiceDepartment::query()->where('slug', 'PED')->sole();

    expect($ped->name)->toBe('Peds')
        ->and(Service::query()->where('name', 'like', 'Paeds M.O%')->pluck('service_department_id')->unique()->all())->toBe([$ped->id]);
});

test('the data migration moves existing Paeds services from OPD to Peds', function () {
    $opd = ServiceDepartment::factory()->create(['slug' => 'OPD', 'name' => 'OPD']);
    $paeds = Service::factory()->create(['name' => 'Paeds M.O Morning', 'service_department_id' => $opd->id]);
    $adult = Service::factory()->create(['name' => 'M.O Morning', 'service_department_id' => $opd->id]);

    $migration = require database_path('migrations/2026_09_26_194405_move_paeds_services_to_ped_department.php');
    $migration->up();

    $ped = ServiceDepartment::query()->where('slug', 'PED')->sole();

    expect($paeds->fresh()->service_department_id)->toBe($ped->id)
        ->and($adult->fresh()->service_department_id)->toBe($opd->id);

    $migration->down();

    expect($paeds->fresh()->service_department_id)->toBe($opd->id);
});

test('a Peds payment at reception creates a PED service order', function () {
    $ped = ServiceDepartment::factory()->create(['slug' => 'PED']);
    $service = Service::factory()->create(['service_department_id' => $ped->id]);
    $transaction = Transaction::factory()->create(['receaveable_id' => null]);

    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_id' => $service->id,
        'type' => 'PED',
        'income_or_expense' => 'INCOME',
    ]);

    $order = ServiceOrder::query()->latest('id')->first();

    expect($order->type)->toBe('PED')
        ->and($order->so_short)->toStartWith('PED/')
        ->and($order->so_number)->toContain('/PED/');
});

test('a Peds doctor sees only their Peds queue on the Peds dashboard', function () {
    $doctor = pedsDoctor();
    actingAs($doctor);

    $mine = ServiceOrder::factory()->create(['type' => 'PED', 'doctor_id' => $doctor->id, 'status' => 'open']);
    ServiceOrder::factory()->create(['type' => 'OPD', 'doctor_id' => $doctor->id, 'status' => 'open']);

    get(route('ped-dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('opd/index')
            ->where('isOpdDoctor', true)
            ->where('department.type', 'PED')
            ->where('department.label', 'Peds')
            ->where('department.apiMyQueueUrl', '/api/ped/my-queue')
            ->has('recentOrders', 1)
            ->where('recentOrders.0.id', $mine->id)
        );

    getJson(route('api-ped-my-queue'))
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.id', $mine->id);
});

test('an OPD-only doctor is not treated as a Peds doctor, and OPD keeps working', function () {
    $doctor = User::factory()->create();
    OpdDoctor::factory()->create(['user_id' => $doctor->id]);
    actingAs($doctor);

    get(route('ped-dashboard'))->assertInertia(fn (Assert $page) => $page->where('isOpdDoctor', false));
    get(route('opd-dashboard'))->assertInertia(fn (Assert $page) => $page
        ->where('isOpdDoctor', true)
        ->where('department.type', 'OPD')
    );
});

test('Peds search and patient page use the Peds department', function () {
    actingAs(pedsDoctor());
    $order = ServiceOrder::factory()->create(['type' => 'PED', 'so_short' => 'PED/00000042']);
    ServiceOrder::factory()->create(['type' => 'OPD', 'so_short' => 'OPD/00000042']);

    postJson(route('api-ped-search'), ['q' => 'PED/0000004'])
        ->assertOk()
        ->assertJsonPath('data.possible.0.id', $order->id)
        ->assertJsonCount(1, 'data.possible');

    get(route('ped-patient', ['id' => $order->id]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('opd/patient')
            ->where('department.type', 'PED')
            ->where('department.apiSaveTreatmentUrlTemplate', '/api/ped/service-orders/__ID__/treatment-record')
        );
});

test('the Peds queue display lists open Peds orders', function () {
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    $order = ServiceOrder::factory()->create(['type' => 'PED', 'status' => 'open']);

    get(route('hospital-ped-queue'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('hospital/opd-queue')
            ->where('departmentLabel', 'Peds')
            ->has("serviceOrdersByService.{$order->service_id}", 1)
        );
});

test('a Peds doctor profile counts as a doctor and is shared with the frontend', function () {
    $doctor = pedsDoctor();

    expect($doctor->isAnyDoctor())->toBeTrue()
        ->and($doctor->profiles['ped_doctor'])->toHaveCount(1)
        ->and(User::cachedDoctors()->pluck('id'))->toContain($doctor->id);
});
