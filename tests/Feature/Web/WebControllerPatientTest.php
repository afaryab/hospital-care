<?php

use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\TreatmentRecord;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

test('patient page loads full service order detail including receivables and divided voucher shares', function () {
    actingAs(User::factory()->create());

    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/07/2620']);
    $serviceOrder = ServiceOrder::factory()->create([
        'patient_id' => $patient->id,
        'type' => 'EMG',
        'so_number' => 'PS/2026/07/2620/EMG/00001334',
        'so_short' => '00001334',
    ]);

    $transaction = Transaction::factory()->create(['patient_id' => $patient->id, 'amount' => 1000]);
    TransactionElement::factory()->create([
        'transaction_id' => $transaction->id,
        'service_order_id' => $serviceOrder->id,
        'income_or_expense' => 'INCOME',
        'amount' => 1000,
    ]);
    Receaveable::factory()->create([
        'patient_id' => $patient->id,
        'transaction_id' => $transaction->id,
        'status' => 'unpaid',
    ]);

    $doctor = User::factory()->create();
    TreatmentRecord::create([
        'service_order_id' => $serviceOrder->id,
        'department_id' => $serviceOrder->service->service_department_id,
        'treating_doctor_id' => $doctor->id,
        'recorded_by' => $doctor->id,
        'treated_at' => now(),
        'chief_complaint' => 'Chest pain',
    ]);

    $otherOrder = ServiceOrder::factory()->create();
    $voucher = ExpenseVoucher::factory()->create(['amount' => 800]);
    $voucher->serviceOrders()->attach([$serviceOrder->id, $otherOrder->id]);

    get('/PS/2026/07/2620/EMG/00001334')
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('patient')
            ->where('serviceOrder.id', $serviceOrder->id)
            ->where('serviceOrder.income_total', 1000)
            ->where('serviceOrder.treatment_record.chief_complaint', 'Chest pain')
            ->has('serviceOrder.receivables', 1)
            ->where('serviceOrder.expense_vouchers.0.share_amount', 400)
        );
});
