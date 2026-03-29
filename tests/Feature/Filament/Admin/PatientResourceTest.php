<?php

use App\Filament\Admin\Resources\Patients\Pages\ListPatients;
use App\Filament\Admin\Resources\Patients\Pages\ViewPatient;
use App\Models\Administrator;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TreatmentRecord;
use App\Models\User;

use function Pest\Laravel\actingAs;

beforeEach(function () {
    $user = User::factory()->create();
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);
    actingAs($user);
});

test('admin can list patients with search', function () {
    $matched = Patient::factory()->create(['name' => 'Ahmad Farooq']);
    $unmatched = Patient::factory()->create(['name' => 'Zulfiqar Khan']);

    Livewire\Livewire::test(ListPatients::class)
        ->assertSuccessful()
        ->searchTable('Ahmad')
        ->assertCanSeeTableRecords([$matched])
        ->assertCanNotSeeTableRecords([$unmatched]);
});

test('admin can view patient with all tabs', function () {
    $patient = Patient::factory()->create();

    Livewire\Livewire::test(ViewPatient::class, ['record' => $patient->getRouteKey()])
        ->assertSuccessful()
        ->assertSee('Overview')
        ->assertSee('Service Orders')
        ->assertSee('Transactions')
        ->assertSee('Receivables')
        ->assertSee('Treatment History');
});

test('patient service orders tab shows related records', function () {
    $patient = Patient::factory()->create();

    $serviceOrder = ServiceOrder::factory()->create([
        'patient_id' => $patient->id,
        'payee_type' => Patient::class,
        'payee_id' => $patient->id,
        'so_number' => 'PS/2026/03/9001/OPD/01',
    ]);

    Livewire\Livewire::test(ViewPatient::class, ['record' => $patient->getRouteKey()])
        ->assertSee($serviceOrder->so_number);
});

test('patient transactions tab shows related records', function () {
    $patient = Patient::factory()->create();

    $transaction = Transaction::factory()->create([
        'patient_id' => $patient->id,
        'tr_number' => 'TR/2026/03/29/9001',
    ]);

    Livewire\Livewire::test(ViewPatient::class, ['record' => $patient->getRouteKey()])
        ->assertSee($transaction->tr_number);
});

test('patient receivables tab shows outstanding amounts', function () {
    $patient = Patient::factory()->create();

    Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'status' => 'PENDING',
        'amount' => 500,
        'orignal_amount' => 500,
    ]);

    Livewire\Livewire::test(ViewPatient::class, ['record' => $patient->getRouteKey()])
        ->assertSee('500.00');
});

test('patient treatment history tab shows related records', function () {
    $patient = Patient::factory()->create();

    $serviceOrder = ServiceOrder::factory()->create([
        'patient_id' => $patient->id,
        'payee_type' => Patient::class,
        'payee_id' => $patient->id,
    ]);

    $treatmentRecord = TreatmentRecord::factory()->create([
        'service_order_id' => $serviceOrder->id,
        'chief_complaint' => 'Severe headache',
    ]);

    Livewire\Livewire::test(ViewPatient::class, ['record' => $patient->getRouteKey()])
        ->assertSee($treatmentRecord->chief_complaint);
});
