<?php

use App\Models\NursingStaff;
use App\Models\ServiceOrder;
use App\Models\TreatmentAttachment;
use App\Models\TreatmentRecord;
use App\Models\User;
use App\Models\XrayTechnician;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

function xrayTechnician(): User
{
    $user = User::factory()->create();
    XrayTechnician::factory()->create(['user_id' => $user->id]);

    return $user;
}

test('uploading an attachment stores it on the private disk, not the public disk', function () {
    Storage::fake('local');
    Storage::fake('public');

    $doctor = xrayTechnician();
    $this->actingAs($doctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'doctor_id' => $doctor->id]);

    $response = $this->postJson("/api/xray/service-orders/{$serviceOrder->id}/attachments", [
        'file' => UploadedFile::fake()->image('chest.jpg'),
    ]);

    $response->assertCreated();

    $attachment = TreatmentAttachment::first();
    Storage::disk('local')->assertExists($attachment->file_path);
    Storage::disk('public')->assertMissing($attachment->file_path);
});

test('a doctor not assigned to the service order can still upload an attachment', function () {
    Storage::fake('local');

    $assignedDoctor = xrayTechnician();
    $otherDoctor = xrayTechnician();
    $this->actingAs($otherDoctor);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'doctor_id' => $assignedDoctor->id]);

    $response = $this->postJson("/api/xray/service-orders/{$serviceOrder->id}/attachments", [
        'file' => UploadedFile::fake()->image('chest.jpg'),
    ]);

    $response->assertCreated();
    expect(TreatmentAttachment::count())->toBe(1);
});

test('a doctor not assigned to the service order can still delete another doctor\'s attachment', function () {
    Storage::fake('local');

    $assignedDoctor = xrayTechnician();
    $otherDoctor = xrayTechnician();

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'doctor_id' => $assignedDoctor->id]);
    $treatmentRecord = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);
    $attachment = TreatmentAttachment::factory()->create(['treatment_record_id' => $treatmentRecord->id]);
    Storage::disk('local')->put($attachment->file_path, 'fake-bytes');

    $this->actingAs($otherDoctor);

    $response = $this->deleteJson("/api/xray/attachments/{$attachment->id}");

    $response->assertOk();
    expect(TreatmentAttachment::find($attachment->id))->toBeNull();
    Storage::disk('local')->assertMissing($attachment->file_path);
});

test('the assigned doctor can delete their own attachment', function () {
    Storage::fake('local');

    $doctor = xrayTechnician();
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'doctor_id' => $doctor->id]);
    $treatmentRecord = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);
    $attachment = TreatmentAttachment::factory()->create(['treatment_record_id' => $treatmentRecord->id]);
    Storage::disk('local')->put($attachment->file_path, 'fake-bytes');

    $this->actingAs($doctor);

    $response = $this->deleteJson("/api/xray/attachments/{$attachment->id}");

    $response->assertOk();
    expect(TreatmentAttachment::find($attachment->id))->toBeNull();
    Storage::disk('local')->assertMissing($attachment->file_path);
});

test('a doctor not assigned to the service order can still view another doctor\'s attachment', function () {
    Storage::fake('local');

    $assignedDoctor = xrayTechnician();
    $otherDoctor = xrayTechnician();

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'doctor_id' => $assignedDoctor->id]);
    $treatmentRecord = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);
    $attachment = TreatmentAttachment::factory()->create([
        'treatment_record_id' => $treatmentRecord->id,
        'file_type' => 'image/jpeg',
    ]);
    Storage::disk('local')->put($attachment->file_path, 'fake-bytes');

    $this->actingAs($otherDoctor);

    $response = $this->get("/api/attachments/{$attachment->id}");

    $response->assertOk();
});

test('the assigned doctor can view their own attachment', function () {
    Storage::fake('local');

    $doctor = xrayTechnician();
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY', 'doctor_id' => $doctor->id]);
    $treatmentRecord = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);
    $attachment = TreatmentAttachment::factory()->create([
        'treatment_record_id' => $treatmentRecord->id,
        'file_type' => 'image/jpeg',
    ]);
    Storage::disk('local')->put($attachment->file_path, 'fake-bytes');

    $this->actingAs($doctor);

    $response = $this->get("/api/attachments/{$attachment->id}");

    $response->assertOk();
    $response->assertHeader('Content-Type', 'image/jpeg');
});

test('a nursing staff member has broad access to view and delete any attachment', function () {
    Storage::fake('local');

    $nurse = User::factory()->create();
    NursingStaff::factory()->create(['user_id' => $nurse->id]);

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $treatmentRecord = TreatmentRecord::factory()->create(['service_order_id' => $serviceOrder->id]);
    $attachment = TreatmentAttachment::factory()->create(['treatment_record_id' => $treatmentRecord->id]);
    Storage::disk('local')->put($attachment->file_path, 'fake-bytes');

    $this->actingAs($nurse);

    $this->get("/api/attachments/{$attachment->id}")->assertOk();
    $this->deleteJson("/api/xray/attachments/{$attachment->id}")->assertOk();
});

test('the attachment url accessor points at the authorized streaming route', function () {
    $attachment = TreatmentAttachment::factory()->make(['id' => 42]);

    expect($attachment->url)->toBe(route('api-attachments-show', 42));
});
