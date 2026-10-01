<?php

use App\Enum\ComplianceStatus;
use App\Filament\Admin\Pages\Compliance;
use App\Models\Administrator;
use App\Models\HospitalSetting;
use App\Models\Patient;
use App\Models\User;
use App\Services\Compliance\ComplianceService;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

function complianceAdmin(): User
{
    $user = User::factory()->create(['two_factor_confirmed_at' => now()]);
    Administrator::create(['user_id' => $user->id, 'authority' => 'administrator']);
    test()->actingAs($user);

    return $user;
}

function complianceCheck(string $key): array
{
    return collect(app(ComplianceService::class)->checks())->firstWhere('key', $key);
}

test('admins can open the compliance page and see every check', function () {
    complianceAdmin();

    Livewire\Livewire::test(Compliance::class)
        ->assertSuccessful()
        ->assertSee('Role-based access control')
        ->assertSee('Patient identifiers encrypted at rest')
        ->assertSee('Periodic risk assessment completed');
});

test('non-admins cannot open the compliance page', function () {
    $this->actingAs(User::factory()->create());

    expect(Compliance::canAccess())->toBeFalse();
});

test('privileged users without two-factor are reported by name', function () {
    complianceAdmin();
    $lax = User::factory()->create(['name' => 'Lax Admin', 'two_factor_confirmed_at' => null]);
    Administrator::create(['user_id' => $lax->id, 'authority' => 'administrator']);

    $check = complianceCheck('mfa');

    expect($check['status'])->not->toBe(ComplianceStatus::Pass)
        ->and(implode(' ', $check['issues']))->toContain('Lax Admin');
});

test('plain-text patient identifiers fail the encryption check', function () {
    complianceAdmin();
    $patient = Patient::factory()->create();

    expect(complianceCheck('phi_encryption')['status'])->toBe(ComplianceStatus::Pass);

    DB::table('patients')->where('id', $patient->id)->update(['cnic' => '35202-1234567-1']);

    $check = complianceCheck('phi_encryption');
    expect($check['status'])->toBe(ComplianceStatus::Fail)
        ->and($check['issues'][0])->toContain('cnic');
});

test('the consent check follows the hospital setting', function () {
    complianceAdmin();

    expect(complianceCheck('consent')['status'])->toBe(ComplianceStatus::Warning);

    HospitalSetting::set('require_consent_before_treatment', true);

    expect(complianceCheck('consent')['status'])->toBe(ComplianceStatus::Pass);
});

test('an admin can attest a manual item, and it is audit logged', function () {
    $admin = complianceAdmin();

    expect(complianceCheck('risk_assessment')['status'])->toBe(ComplianceStatus::Pending);

    Livewire\Livewire::test(Compliance::class)
        ->callAction(TestAction::make('attest')->arguments(['key' => 'risk_assessment']), ['note' => 'Signed off on 1 Sep'])
        ->assertNotified();

    $check = complianceCheck('risk_assessment');
    expect($check['status'])->toBe(ComplianceStatus::Attested)
        ->and($check['attestation']['attested_by'])->toBe($admin->id)
        ->and($check['attestation']['note'])->toBe('Signed off on 1 Sep');

    expect(Activity::query()->where('event', 'compliance_attested')->exists())->toBeTrue();
});

test('attestations older than a year are pending again', function () {
    $admin = complianceAdmin();

    $this->travelTo(now()->subYears(2));
    app(ComplianceService::class)->attest('workforce_training', $admin);
    $this->travelBack();

    expect(complianceCheck('workforce_training')['status'])->toBe(ComplianceStatus::Pending);
});
