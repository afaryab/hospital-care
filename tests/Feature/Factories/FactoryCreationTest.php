<?php

use App\Models\Accountant;
use App\Models\Administrator;
use App\Models\Asset;
use App\Models\AssetAssignmentLog;
use App\Models\AssetCategory;
use App\Models\AssetDepreciationEntry;
use App\Models\AssetMaintenanceLog;
use App\Models\Dentist;
use App\Models\EmergencyDoctor;
use App\Models\HospitalSetting;
use App\Models\Icd10Code;
use App\Models\IndDoctor;
use App\Models\NursingStaff;
use App\Models\OpdDoctor;
use App\Models\Panel;
use App\Models\PatientManager;
use App\Models\PayrollPeriod;
use App\Models\PayslipEntry;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Receptionist;
use App\Models\SalaryAdvance;
use App\Models\SalaryStructure;
use App\Models\StockCategory;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Models\Task;
use App\Models\TaskComment;
use App\Models\UltrasoundDoctor;
use App\Models\XrayTechnician;

// Phase 1.1
test('administrator factory creates valid model', function () {
    expect(Administrator::factory()->create()->exists)->toBeTrue();
});

test('accountant factory creates valid model', function () {
    expect(Accountant::factory()->create()->exists)->toBeTrue();
});

test('receptionist factory creates valid model', function () {
    expect(Receptionist::factory()->create()->exists)->toBeTrue();
});

test('opd doctor factory creates valid model', function () {
    expect(OpdDoctor::factory()->create()->exists)->toBeTrue();
});

test('ind doctor factory creates valid model', function () {
    expect(IndDoctor::factory()->create()->exists)->toBeTrue();
});

test('emergency doctor factory creates valid model', function () {
    expect(EmergencyDoctor::factory()->create()->exists)->toBeTrue();
});

test('dentist factory creates valid model', function () {
    expect(Dentist::factory()->create()->exists)->toBeTrue();
});

test('ultrasound doctor factory creates valid model', function () {
    expect(UltrasoundDoctor::factory()->create()->exists)->toBeTrue();
});

test('xray technician factory creates valid model', function () {
    expect(XrayTechnician::factory()->create()->exists)->toBeTrue();
});

test('nursing staff factory creates valid model', function () {
    expect(NursingStaff::factory()->create()->exists)->toBeTrue();
});

test('patient manager factory creates valid model', function () {
    expect(PatientManager::factory()->create()->exists)->toBeTrue();
});

test('panel factory creates valid model', function () {
    expect(Panel::factory()->create()->exists)->toBeTrue();
});

// Phase 1.3
test('hospital setting factory creates model with key', function () {
    $model = HospitalSetting::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->key)->not->toBeEmpty();
});

// Phase 1.5
test('icd10 code factory creates model with code', function () {
    $model = Icd10Code::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->code)->not->toBeEmpty();
});

// Phase 1.6
test('stock category factory creates valid model', function () {
    expect(StockCategory::factory()->create()->exists)->toBeTrue();
});

test('stock category medicine state sets is_medicine true', function () {
    expect(StockCategory::factory()->medicine()->create()->is_medicine)->toBeTrue();
});

test('stock item factory creates valid model', function () {
    expect(StockItem::factory()->create()->exists)->toBeTrue();
});

test('stock movement factory creates valid model', function () {
    expect(StockMovement::factory()->create()->exists)->toBeTrue();
});

test('stock movement out state sets type to OUT', function () {
    expect(StockMovement::factory()->out()->create()->type->value)->toBe('OUT');
});

test('purchase order factory creates model with po number', function () {
    $model = PurchaseOrder::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->po_number)->not->toBeEmpty();
});

test('purchase order approved state works', function () {
    expect(PurchaseOrder::factory()->approved()->create()->status->value)->toBe('approved');
});

test('purchase order item factory creates valid model', function () {
    expect(PurchaseOrderItem::factory()->create()->exists)->toBeTrue();
});

// Phase 1.7
test('asset category factory creates valid model', function () {
    expect(AssetCategory::factory()->create()->exists)->toBeTrue();
});

test('asset factory creates model with ast number', function () {
    $model = Asset::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->asset_number)->not->toBeEmpty();
});

test('asset under maintenance state works', function () {
    expect(Asset::factory()->underMaintenance()->create()->status->value)->toBe('under_maintenance');
});

test('asset assignment log factory creates valid model', function () {
    expect(AssetAssignmentLog::factory()->create()->exists)->toBeTrue();
});

test('asset maintenance log factory creates valid model', function () {
    expect(AssetMaintenanceLog::factory()->create()->exists)->toBeTrue();
});

test('asset depreciation entry factory creates valid model', function () {
    $model = AssetDepreciationEntry::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->depreciation_amount)->toBeGreaterThan(0);
});

// Phase 1.8
test('task factory creates model with task number', function () {
    $model = Task::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->task_number)->not->toBeEmpty();
});

test('task factory completed state works', function () {
    expect(Task::factory()->completed()->create()->status->value)->toBe('completed');
});

test('task comment factory creates valid model', function () {
    expect(TaskComment::factory()->create()->exists)->toBeTrue();
});

// Phase 1.9
test('payroll period factory creates model with period number', function () {
    $model = PayrollPeriod::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->period_number)->not->toBeEmpty();
});

test('salary structure factory creates valid model', function () {
    $model = SalaryStructure::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->basic_salary)->toBeGreaterThan(0);
});

test('payslip entry factory creates valid model', function () {
    $model = PayslipEntry::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->net_salary)->toBeGreaterThan(0);
});

test('salary advance factory creates valid model', function () {
    $model = SalaryAdvance::factory()->create();
    expect($model->exists)->toBeTrue()
        ->and($model->remaining_balance)->toBeGreaterThan(0);
});

test('salary advance fully recovered state sets balance to zero', function () {
    $model = SalaryAdvance::factory()->fullyRecovered()->create();
    expect($model->status->value)->toBe('fully_recovered')
        ->and((float) $model->remaining_balance)->toBe(0.0);
});
