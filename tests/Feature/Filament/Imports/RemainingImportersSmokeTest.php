<?php

/**
 * A "does creating a brand-new record through this importer actually save"
 * smoke test for every Importer not already covered by its own dedicated
 * test file. This is exactly the kind of test that caught three real bugs
 * in ServiceImporter/ServiceRecestationImporter/ServiceDepartmentImporter
 * (columns with no database default that the importer wasn't setting) — so
 * every remaining importer gets at least this minimal guarantee.
 */
use App\Filament\Imports\AssetCategoryImporter;
use App\Filament\Imports\AssetImporter;
use App\Filament\Imports\BankAccountImporter;
use App\Filament\Imports\DrugCategoryImporter;
use App\Filament\Imports\DrugImporter;
use App\Filament\Imports\PanelChequeImporter;
use App\Filament\Imports\PanelImporter;
use App\Filament\Imports\PatientImporter;
use App\Filament\Imports\ServiceDepartmentImporter;
use App\Filament\Imports\ServiceRecestationImporter;
use App\Filament\Imports\StockCategoryImporter;
use App\Filament\Imports\StockItemImporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Imports\WardImporter;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\BankAccount;
use App\Models\Drug;
use App\Models\DrugCategory;
use App\Models\Panel;
use App\Models\PanelCheque;
use App\Models\Patient;
use App\Models\ServiceDepartment;
use App\Models\ServiceRecestation;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\User;
use App\Models\Ward;
use Illuminate\Support\Facades\Hash;

beforeEach(function () {
    $this->actingAs(User::factory()->create());
});

test('AssetCategoryImporter creates a new record', function () {
    (new AssetCategoryImporter(makeFilamentImport(AssetCategoryImporter::class), ['name' => 'name'], []))([
        'name' => 'IT Equipment',
    ]);

    expect(AssetCategory::where('name', 'IT Equipment')->exists())->toBeTrue();
});

test('AssetImporter creates a new record resolving its category by name', function () {
    $category = AssetCategory::factory()->create(['name' => 'IT Equipment']);

    (new AssetImporter(makeFilamentImport(AssetImporter::class), [
        'name' => 'name',
        'category' => 'category',
    ], []))([
        'name' => 'Dell Laptop',
        'category' => 'IT Equipment',
    ]);

    $asset = Asset::where('name', 'Dell Laptop')->first();
    expect($asset)->not->toBeNull()
        ->and($asset->category_id)->toBe($category->id)
        ->and($asset->asset_number)->not->toBeNull();
});

test('BankAccountImporter creates a new record', function () {
    (new BankAccountImporter(makeFilamentImport(BankAccountImporter::class), [
        'name' => 'name',
        'bank_name' => 'bank_name',
        'account_number' => 'account_number',
    ], []))([
        'name' => 'Main Account',
        'bank_name' => 'HBL',
        'account_number' => '1234567890',
    ]);

    expect(BankAccount::where('account_number', '1234567890')->exists())->toBeTrue();
});

test('PanelImporter creates a new record', function () {
    (new PanelImporter(makeFilamentImport(PanelImporter::class), [
        'name' => 'name',
        'code' => 'code',
    ], []))([
        'name' => 'State Life',
        'code' => 'SL01',
    ]);

    expect(Panel::where('code', 'SL01')->exists())->toBeTrue();
});

test('ServiceDepartmentImporter creates a new record', function () {
    (new ServiceDepartmentImporter(makeFilamentImport(ServiceDepartmentImporter::class), ['name' => 'name'], []))([
        'name' => 'Radiology',
    ]);

    expect(ServiceDepartment::where('name', 'Radiology')->exists())->toBeTrue();
});

test('PanelChequeImporter creates a new record resolving panel and bank account', function () {
    $panel = Panel::factory()->create(['name' => 'State Life']);
    $bankAccount = BankAccount::create([
        'name' => 'Main Account',
        'bank_name' => 'HBL',
        'account_number' => '1234567890',
    ]);

    (new PanelChequeImporter(makeFilamentImport(PanelChequeImporter::class), [
        'panel' => 'panel',
        'bankAccount' => 'bankAccount',
        'cheque_number' => 'cheque_number',
        'amount' => 'amount',
    ], []))([
        'panel' => 'State Life',
        'bankAccount' => '1234567890',
        'cheque_number' => 'CHQ-001',
        'amount' => '10000',
    ]);

    $cheque = PanelCheque::where('cheque_number', 'CHQ-001')->first();
    expect($cheque)->not->toBeNull()
        ->and($cheque->panel_id)->toBe($panel->id)
        ->and($cheque->bank_account_id)->toBe($bankAccount->id);
});

test('UserImporter creates a new account with a random password, not a plaintext one', function () {
    (new UserImporter(makeFilamentImport(UserImporter::class), [
        'name' => 'name',
        'username' => 'username',
        'email' => 'email',
    ], []))([
        'name' => 'Jane Doe',
        'username' => 'janedoe',
        'email' => 'jane@example.test',
    ]);

    $user = User::where('email', 'jane@example.test')->first();
    expect($user)->not->toBeNull()
        ->and((bool) $user->is_active)->toBeTrue()
        ->and($user->password)->not->toBeNull()
        ->and(Hash::check('password', $user->password))->toBeFalse();
});

test('PatientImporter creates a new record with an auto-generated ps_number', function () {
    (new PatientImporter(makeFilamentImport(PatientImporter::class), [
        'name' => 'name',
        'gender' => 'gender',
        'cnic' => 'cnic',
    ], []))([
        'name' => 'John Smith',
        'gender' => 'm',
        'cnic' => '35202-1234567-1',
    ]);

    $patient = Patient::where('name', 'John Smith')->first();
    expect($patient)->not->toBeNull()
        ->and($patient->ps_number)->not->toBeNull()
        ->and($patient->cnic)->toBe('35202-1234567-1')
        ->and($patient->cnic_hash)->not->toBeNull();
});

test('re-importing a patient with the same CNIC updates the existing record instead of duplicating', function () {
    (new PatientImporter(makeFilamentImport(PatientImporter::class), [
        'name' => 'name',
        'cnic' => 'cnic',
    ], []))(['name' => 'John Smith', 'cnic' => '35202-1234567-1']);

    (new PatientImporter(makeFilamentImport(PatientImporter::class), [
        'name' => 'name',
        'cnic' => 'cnic',
    ], []))(['name' => 'John A. Smith', 'cnic' => '35202-1234567-1']);

    expect(Patient::count())->toBe(1)
        ->and(Patient::first()->name)->toBe('John A. Smith');
});

test('DrugImporter creates a new record', function () {
    (new DrugImporter(makeFilamentImport(DrugImporter::class), ['name' => 'name'], []))([
        'name' => 'Panadol',
    ]);

    expect(Drug::where('name', 'Panadol')->exists())->toBeTrue();
});

test('ServiceRecestationImporter creates a new record resolving its department by name', function () {
    $department = ServiceDepartment::factory()->create(['name' => 'OPD']);

    (new ServiceRecestationImporter(makeFilamentImport(ServiceRecestationImporter::class), [
        'name' => 'name',
        'department' => 'department',
        'charges' => 'charges',
    ], []))([
        'name' => 'Follow-up Visit',
        'department' => 'OPD',
        'charges' => '200',
    ]);

    $recestation = ServiceRecestation::where('name', 'Follow-up Visit')->first();
    expect($recestation)->not->toBeNull()
        ->and($recestation->service_department_id)->toBe($department->id);
});

test('WardImporter creates a new record', function () {
    (new WardImporter(makeFilamentImport(WardImporter::class), ['name' => 'name'], []))([
        'name' => 'General Ward A',
    ]);

    expect(Ward::where('name', 'General Ward A')->exists())->toBeTrue();
});

test('DrugCategoryImporter creates a new record', function () {
    (new DrugCategoryImporter(makeFilamentImport(DrugCategoryImporter::class), ['name' => 'name'], []))([
        'name' => 'Antibiotics',
    ]);

    expect(DrugCategory::where('name', 'Antibiotics')->exists())->toBeTrue();
});

test('StockCategoryImporter creates a new record', function () {
    (new StockCategoryImporter(makeFilamentImport(StockCategoryImporter::class), ['name' => 'name'], []))([
        'name' => 'Surgical Supplies',
    ]);

    expect(StockCategory::where('name', 'Surgical Supplies')->exists())->toBeTrue();
});

test('StockItemImporter creates a new record resolving its category by name', function () {
    $category = StockCategory::factory()->create(['name' => 'Surgical Supplies']);

    (new StockItemImporter(makeFilamentImport(StockItemImporter::class), [
        'name' => 'name',
        'sku' => 'sku',
        'category' => 'category',
        'unit' => 'unit',
    ], []))([
        'name' => 'Gauze Roll',
        'sku' => 'GZ-001',
        'category' => 'Surgical Supplies',
        'unit' => 'pcs',
    ]);

    $item = StockItem::where('name', 'Gauze Roll')->first();
    expect($item)->not->toBeNull()
        ->and($item->category_id)->toBe($category->id);
});
