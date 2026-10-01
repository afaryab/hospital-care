<?php

use App\Models\Closing;
use App\Models\Patient;
use App\Models\Reception;
use App\Models\Receptionist;
use App\Models\Service;
use App\Models\ServiceDepartment;
use App\Models\Transaction;
use App\Models\User;
use Database\Seeders\ServicesAndDepartmentsSeeder;
use Illuminate\Testing\TestResponse;

/**
 * @param  list<string>|null  $allowedDepartments
 * @return array{0: User, 1: Reception}
 */
function receptionistAtCounter(?array $allowedDepartments): array
{
    $user = User::factory()->create();
    $reception = Reception::factory()->create(['allowed_departments' => $allowedDepartments]);
    Receptionist::factory()->create(['user_id' => $user->id, 'reception_id' => $reception->id]);
    Closing::factory()->create(['receptionist_id' => $user->id, 'reception_id' => $reception->id, 'status' => 'open']);

    return [$user, $reception];
}

function postPedsSlip(): TestResponse
{
    $patient = Patient::factory()->create();
    $service = Service::query()
        ->where('service_department_id', ServiceDepartment::query()->where('slug', 'PED')->value('id'))
        ->firstOrFail();

    return test()->post(route('transaction-store'), [
        'income_or_expense' => 'INCOME',
        'patient_id' => $patient->id,
        'department_key' => 'PED',
        'total_amount' => 500,
        'payment_method' => 'CASH',
        'amount_paid' => 500,
        'items' => [
            ['service_id' => $service->id, 'service_name' => $service->name, 'quantity' => 1, 'unit_price' => '500.00', 'total' => 500, 'provider_id' => null],
        ],
    ]);
}

function pedReceptionMigration(): object
{
    return require database_path('migrations/2026_10_01_184057_add_ped_to_receptions_allowing_opd.php');
}

beforeEach(function () {
    $this->seed(ServicesAndDepartmentsSeeder::class);
});

test('a counter allowed every department can bill Peds', function () {
    [$user] = receptionistAtCounter(null);

    $this->actingAs($user);
    postPedsSlip()->assertSessionHasNoErrors()->assertRedirectContains('/TR/');

    expect(Transaction::count())->toBe(1);
});

test('a counter not allowed PED is rejected with a reason the bill screen can show', function () {
    [$user] = receptionistAtCounter(['OPD']);

    $this->actingAs($user);
    postPedsSlip()->assertSessionHasErrors('message');

    expect(session('errors')->first('message'))->toContain('not allowed to process transactions for the PED department')
        ->and(Transaction::count())->toBe(0);
});

test('the migration lets counters that could bill OPD bill Peds again', function () {
    [$user, $opdReception] = receptionistAtCounter(['OPD', 'EMG']);
    $emgOnly = Reception::factory()->create(['allowed_departments' => ['EMG']]);
    $unrestricted = Reception::factory()->create(['allowed_departments' => null]);
    $alreadyPed = Reception::factory()->create(['allowed_departments' => ['OPD', 'PED']]);

    pedReceptionMigration()->up();

    expect($opdReception->fresh()->allowed_departments)->toBe(['OPD', 'EMG', 'PED'])
        ->and($emgOnly->fresh()->allowed_departments)->toBe(['EMG'])
        ->and($unrestricted->fresh()->allowed_departments)->toBeNull()
        ->and($alreadyPed->fresh()->allowed_departments)->toBe(['OPD', 'PED']);

    $this->actingAs($user);
    postPedsSlip()->assertSessionHasNoErrors()->assertRedirectContains('/TR/');

    pedReceptionMigration()->down();

    expect($opdReception->fresh()->allowed_departments)->toBe(['OPD', 'EMG'])
        ->and($alreadyPed->fresh()->allowed_departments)->toBe(['OPD', 'PED']);
});
