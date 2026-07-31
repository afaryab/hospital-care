<?php

use App\Http\Controllers\Prints\ClosingStatementPdfPrintController;
use App\Models\Closing;
use App\Models\Patient;
use App\Models\Receaveable;
use App\Models\Service;
use App\Models\Transaction;
use App\Models\TransactionElement;
use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\get;

function buildClosingForPrintTest(): Closing
{
    $closing = Closing::factory()->create();

    $serviceA = Service::factory()->create(['name' => 'Consultation']);
    $serviceB = Service::factory()->create(['name' => 'Ultrasound Scan']);
    $doctorX = User::factory()->create(['name' => 'Dr. X']);
    $doctorY = User::factory()->create(['name' => 'Dr. Y']);
    $patient = Patient::factory()->create();

    // Two income transactions under Service A / Dr X — should merge into one group.
    foreach ([500, 200] as $amount) {
        $transaction = Transaction::factory()->create([
            'closing_id' => $closing->id,
            'income_or_expense' => 'INCOME',
            'amount' => $amount,
            'orignal_amount' => $amount,
        ]);
        TransactionElement::factory()->create([
            'closing_id' => $closing->id,
            'transaction_id' => $transaction->id,
            'service_id' => $serviceA->id,
            'doctor_id' => $doctorX->id,
            'amount' => $amount,
            'orignal_amount' => $amount,
        ]);
    }

    // One income transaction under Service B / Dr Y.
    $txB = Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 300,
        'orignal_amount' => 300,
    ]);
    TransactionElement::factory()->create([
        'closing_id' => $closing->id,
        'transaction_id' => $txB->id,
        'service_id' => $serviceB->id,
        'doctor_id' => $doctorY->id,
        'amount' => 300,
        'orignal_amount' => 300,
    ]);

    // One income transaction with an element that has no service — must not
    // be dropped, should land under "Uncategorized" and still count toward totals.
    $txNoService = Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'amount' => 100,
        'orignal_amount' => 100,
    ]);
    TransactionElement::factory()->create([
        'closing_id' => $closing->id,
        'transaction_id' => $txNoService->id,
        'service_id' => null,
        'doctor_id' => null,
        'amount' => 100,
        'orignal_amount' => 100,
    ]);

    // A receivable created during this closing (full amount not collected yet).
    $txReceivableOrigin = Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'patient_id' => $patient->id,
        'amount' => 1000,
        'orignal_amount' => 1000,
    ]);
    $receaveable = Receaveable::factory()->create([
        'transaction_id' => $txReceivableOrigin->id,
        'patient_id' => $patient->id,
        'orignal_amount' => 1000,
        'amount' => 1000,
        'status' => 'unpaid',
    ]);

    // A settlement transaction collecting part of that receivable, within the same closing.
    Transaction::factory()->create([
        'closing_id' => $closing->id,
        'income_or_expense' => 'INCOME',
        'patient_id' => $patient->id,
        'type' => 'CASH',
        'amount' => 400,
        'orignal_amount' => 400,
        'receaveable_id' => $receaveable->id,
    ]);

    return $closing->fresh([
        'transactions.elements.patient',
        'transactions.elements.service',
        'transactions.elements.doctor',
        'transactions.elements.expenseCategory',
        'transactions.elements.serviceRecestation',
        'transactions.receaveable.patient',
        'transactions.receaveable.panel',
        'transactions.settledReceaveable.patient',
    ]);
}

test('closing statement print groups income by service and provider, including uncategorized', function () {
    $closing = buildClosingForPrintTest();

    $controller = new ClosingStatementPdfPrintController;
    $method = new ReflectionMethod($controller, 'prepareClosingData');
    $data = $method->invoke($controller, $closing);

    $groups = collect($data['service_groups'])->keyBy('service_name');

    expect($groups->has('Consultation'))->toBeTrue()
        ->and((float) $groups['Consultation']['total_income'])->toBe(700.0)
        ->and($groups['Consultation']['providers'][0]['doctor_name'])->toBe('Dr. X')
        ->and($groups->has('Ultrasound Scan'))->toBeTrue()
        ->and((float) $groups['Ultrasound Scan']['total_income'])->toBe(300.0)
        ->and($groups->has('Uncategorized'))->toBeTrue()
        ->and((float) $groups['Uncategorized']['total_income'])->toBe(100.0);
});

test('closing statement print reports receivables created and collected as separate totals with row-level records', function () {
    $closing = buildClosingForPrintTest();

    $controller = new ClosingStatementPdfPrintController;
    $method = new ReflectionMethod($controller, 'prepareClosingData');
    $data = $method->invoke($controller, $closing);

    expect((float) $data['totals']['receivables_created_total'])->toBe(1000.0)
        ->and((float) $data['totals']['receivables_collected_total'])->toBe(400.0)
        ->and($data['receivables']['created'])->toHaveCount(1)
        ->and((float) $data['receivables']['created'][0]['amount'])->toBe(1000.0)
        ->and($data['receivables']['collected'])->toHaveCount(1)
        ->and((float) $data['receivables']['collected'][0]['amount'])->toBe(400.0);
});

test('closing statement print renders successfully with grouped services and receivables', function () {
    actingAs(User::factory()->create());

    $closing = buildClosingForPrintTest();

    get(route('print-closing-statement', [
        'year' => $closing->year,
        'month' => $closing->month,
        'number' => $closing->number,
    ]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');
});
