<?php

use App\Models\Closing;
use App\Models\ExpenseVoucher;
use App\Models\Patient;
use App\Models\Receptionist;
use App\Models\Transaction;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

test('lookup endpoint returns expected structure', function () {
    $response = $this->getJson('/api/lookup');

    $response->assertOk()
        ->assertJsonStructure(['data']);
});

test('unauthenticated users cannot access lookup', function () {
    auth()->logout();

    $this->getJson('/api/lookup')->assertUnauthorized();
});

function lookupAsReceptionist(): User
{
    $user = User::factory()->create();
    Receptionist::factory()->create(['user_id' => $user->id]);
    test()->actingAs($user);

    return $user;
}

test('lookup finds a patient by ps number prefix and links to the profile', function () {
    lookupAsReceptionist();
    $patient = Patient::factory()->create(['ps_number' => 'PS/2026/03/0042', 'name' => 'Ayesha Khan']);

    $this->getJson('/api/lookup?q=ps/2026/03/004')
        ->assertOk()
        ->assertJsonPath('data.0.group', 'Patients')
        ->assertJsonPath('data.0.title', 'Ayesha Khan')
        ->assertJsonPath('data.0.url', '/PS/2026/03/0042');
});

test('lookup finds patients by name', function () {
    lookupAsReceptionist();
    Patient::factory()->create(['name' => 'Zainab Tariq']);
    Patient::factory()->create(['name' => 'Bilal Ahmed']);

    $this->getJson('/api/lookup?q=zain')
        ->assertOk()
        ->assertJsonCount(1, 'data')
        ->assertJsonPath('data.0.title', 'Zainab Tariq');
});

test('lookup finds patients by cnic and by contact number via their hashes', function () {
    lookupAsReceptionist();
    $patient = Patient::factory()->create(['cnic' => '35202-1234567-1', 'contact' => '03001234567']);

    $this->getJson('/api/lookup?q=3520212345671')->assertJsonPath('data.0.subtitle', $patient->ps_number);
    $this->getJson('/api/lookup?q=0300-1234567')->assertJsonPath('data.0.subtitle', $patient->ps_number);
});

test('lookup finds closings, transactions and expense vouchers by number', function () {
    lookupAsReceptionist();
    $closing = Closing::factory()->create();
    $transaction = Transaction::factory()->create(['closing_id' => $closing->id]);
    $voucher = ExpenseVoucher::factory()->create();

    $this->getJson('/api/lookup?q='.urlencode($closing->ct_number))
        ->assertJsonPath('data.0.group', 'Closings')
        ->assertJsonPath('data.0.url', "/CT/{$closing->year}/{$closing->month}/{$closing->number}");

    $this->getJson('/api/lookup?q='.urlencode($transaction->tr_number))
        ->assertJsonPath('data.0.group', 'Transactions')
        ->assertJsonPath('data.0.title', $transaction->tr_number);

    $this->getJson('/api/lookup?q='.urlencode($voucher->vc_number))
        ->assertJsonPath('data.0.group', 'Expense vouchers');
});

test('lookup does not reveal patients the user may not view', function () {
    Patient::factory()->create(['name' => 'Hidden Person', 'ps_number' => 'PS/2026/03/0077']);

    $this->getJson('/api/lookup?q=Hidden')->assertOk()->assertJsonCount(0, 'data');
    $this->getJson('/api/lookup?q=PS/2026/03/00')->assertOk()->assertJsonCount(0, 'data');
});

test('lookup ignores queries shorter than two characters', function () {
    lookupAsReceptionist();
    Patient::factory()->create(['name' => 'A']);

    $this->getJson('/api/lookup?q=A')->assertOk()->assertJsonCount(0, 'data');
});
