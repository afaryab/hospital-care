<?php

use App\Filament\Admin\Pages\HospitalSettings;
use App\Models\Administrator;
use App\Models\Closing;
use App\Models\HospitalSetting;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('admin can view settings page', function () {
    $user = User::factory()->create();
    Administrator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    Livewire\Livewire::test(HospitalSettings::class)
        ->assertSuccessful();
});

test('admin can update hospital name and address', function () {
    $user = User::factory()->create();
    Administrator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    Livewire\Livewire::test(HospitalSettings::class)
        ->fillForm([
            'hospital_name' => 'City Care Hospital',
            'address' => 'Main Boulevard, Lahore',
            'phone' => '042-111-222333',
            'email' => 'info@citycare.test',
            'ntn' => '1234567-8',
            'strn' => '17-00-1234-567-89',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect(HospitalSetting::get('hospital_name'))->toBe('City Care Hospital')
        ->and(HospitalSetting::get('hospital_address'))->toBe('Main Boulevard, Lahore');
});

test('the consent gate defaults to disabled and can be toggled on', function () {
    $user = User::factory()->create();
    Administrator::factory()->create(['user_id' => $user->id]);
    actingAs($user);

    expect(HospitalSetting::get('require_consent_before_treatment', false))->toBeFalse();

    Livewire\Livewire::test(HospitalSettings::class)
        ->assertFormSet(['require_consent_before_treatment' => false])
        ->fillForm(['require_consent_before_treatment' => true])
        ->call('save')
        ->assertHasNoFormErrors();

    expect((bool) HospitalSetting::get('require_consent_before_treatment'))->toBeTrue();
});

test('settings appear on printed pdf headers', function () {
    HospitalSetting::set('hospital_name', 'City Care Hospital');
    HospitalSetting::set('hospital_address', 'Main Boulevard, Lahore');
    HospitalSetting::set('hospital_ntn', '1234567-8');
    HospitalSetting::set('hospital_strn', '17-00-1234-567-89');

    $closing = Closing::factory()->create();

    $html = view('pdfs.closing-statement.partials.report-header', [
        'report_title' => 'Income Report',
        'report_color' => '#059669',
        'closing' => $closing,
        'reception' => $closing->reception,
        'receptionist' => $closing->receptionist,
        'generated_at' => now(),
    ])->render();

    expect($html)->toContain('City Care Hospital')
        ->and($html)->toContain('Main Boulevard, Lahore')
        ->and($html)->toContain('1234567-8')
        ->and($html)->toContain('17-00-1234-567-89');
});

test('ntn strn appear on transaction receipts', function () {
    HospitalSetting::set('hospital_name', 'City Care Hospital');
    HospitalSetting::set('hospital_address', 'Main Boulevard, Lahore');
    HospitalSetting::set('hospital_phone', '042-111-222333');
    HospitalSetting::set('hospital_email', 'info@citycare.test');
    HospitalSetting::set('hospital_ntn', '1234567-8');
    HospitalSetting::set('hospital_strn', '17-00-1234-567-89');

    $transaction = Transaction::factory()->create();

    $html = view('pdfs.transaction.transaction-full', [
        'transaction' => $transaction->load(['patient', 'elements', 'closing']),
        'patient' => $transaction->patient,
        'receaveable' => $transaction->receaveable,
        'items' => $transaction->elements,
        'counter' => $transaction->closing,
        'variant' => 'full',
        'generated_at' => now(),
        'hospital_info' => [
            'name' => HospitalSetting::get('hospital_name'),
            'address' => HospitalSetting::get('hospital_address'),
            'phone' => HospitalSetting::get('hospital_phone'),
            'email' => HospitalSetting::get('hospital_email'),
            'ntn' => HospitalSetting::get('hospital_ntn'),
            'strn' => HospitalSetting::get('hospital_strn'),
        ],
    ])->render();

    expect($html)->toContain('NTN: 1234567-8')
        ->and($html)->toContain('STRN: 17-00-1234-567-89');
});
