<?php

use App\Models\EmergencyDoctor;
use App\Models\ServiceOrder;
use App\Models\Triage;
use App\Models\TriageHistory;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->doctor = User::factory()->create();
    EmergencyDoctor::factory()->create(['user_id' => $this->doctor->id]);
    $this->actingAs($this->doctor);
});

test('triage_id and treated_at are required for EMG service orders', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'chief_complaint' => 'Chest pain',
    ]);

    $response->assertUnprocessable()
        ->assertJsonValidationErrors(['triage_id', 'treated_at']);
});

test('triage_id and treated_at are not required for non-EMG departments', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'DNT']);

    $response = $this->postJson("/api/dnt/service-orders/{$serviceOrder->id}/treatment-record", [
        'chief_complaint' => 'Toothache',
    ]);

    $response->assertOk();
});

test('assigning a triage on a new EMG treatment record logs initial triage history', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();

    $response = $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'chief_complaint' => 'Shortness of breath',
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
    ]);

    $response->assertOk();

    $treatmentRecord = $serviceOrder->fresh()->treatmentRecord;
    expect($treatmentRecord->triage_id)->toBe($triage->id);

    $history = TriageHistory::where('treatment_record_id', $treatmentRecord->id)->first();
    expect($history)->not->toBeNull();
    expect($history->old_triage_id)->toBeNull();
    expect($history->new_triage_id)->toBe($triage->id);
    expect($history->changed_by)->toBe($this->doctor->id);
});

test('changing triage on an existing treatment record logs the transition', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $initialTriage = Triage::factory()->create();
    $newTriage = Triage::factory()->create();

    $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $initialTriage->id,
        'treated_at' => now()->toIso8601String(),
    ])->assertOk();

    $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $newTriage->id,
        'treated_at' => now()->toIso8601String(),
    ])->assertOk();

    $treatmentRecord = $serviceOrder->fresh()->treatmentRecord;
    expect($treatmentRecord->triage_id)->toBe($newTriage->id);
    expect(TriageHistory::where('treatment_record_id', $treatmentRecord->id)->count())->toBe(2);

    $latest = TriageHistory::where('treatment_record_id', $treatmentRecord->id)->latest('id')->first();
    expect($latest->old_triage_id)->toBe($initialTriage->id);
    expect($latest->new_triage_id)->toBe($newTriage->id);
});

test('resaving with the same triage does not create a duplicate history entry', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();

    $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
    ])->assertOk();

    $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => now()->toIso8601String(),
        'chief_complaint' => 'Updated complaint',
    ])->assertOk();

    $treatmentRecord = $serviceOrder->fresh()->treatmentRecord;
    expect(TriageHistory::where('treatment_record_id', $treatmentRecord->id)->count())->toBe(1);
});

test('submitted treated_at is persisted rather than overwritten with now', function () {
    $serviceOrder = ServiceOrder::factory()->create(['type' => 'EMG']);
    $triage = Triage::factory()->create();
    $treatedAt = now()->subHours(2);

    $this->postJson("/api/emg/service-orders/{$serviceOrder->id}/treatment-record", [
        'triage_id' => $triage->id,
        'treated_at' => $treatedAt->toIso8601String(),
    ])->assertOk();

    $treatmentRecord = $serviceOrder->fresh()->treatmentRecord;
    expect($treatmentRecord->treated_at->diffInSeconds($treatedAt))->toBeLessThan(2);
});

test('my-queue defaults to most recently created service orders first', function () {
    $older = ServiceOrder::factory()->create([
        'type' => 'EMG', 'doctor_id' => $this->doctor->id, 'status' => 'open', 'created_at' => now()->subHour(),
    ]);
    $newer = ServiceOrder::factory()->create([
        'type' => 'EMG', 'doctor_id' => $this->doctor->id, 'status' => 'open', 'created_at' => now(),
    ]);

    $response = $this->getJson('/api/emg/my-queue?'.http_build_query(['types' => ['EMG']]));

    $response->assertOk();
    $ids = collect($response->json('data'))->pluck('id')->values();
    expect($ids->search($newer->id))->toBeLessThan($ids->search($older->id));
});

test('doctor can upload and delete a treatment attachment', function () {
    Storage::fake('public');

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY']);
    $file = UploadedFile::fake()->image('xray.jpg');

    $uploadResponse = $this->postJson("/api/xray/service-orders/{$serviceOrder->id}/attachments", [
        'file' => $file,
    ]);

    $uploadResponse->assertCreated();
    $attachmentId = $uploadResponse->json('data.id');

    $this->assertDatabaseHas('treatment_attachments', [
        'id' => $attachmentId,
        'file_name' => 'xray.jpg',
    ]);
    Storage::disk('public')->assertExists($uploadResponse->json('data.file_path'));

    $deleteResponse = $this->deleteJson("/api/xray/attachments/{$attachmentId}");
    $deleteResponse->assertOk();

    $this->assertDatabaseMissing('treatment_attachments', ['id' => $attachmentId]);
    Storage::disk('public')->assertMissing($uploadResponse->json('data.file_path'));
});

test('attachment upload rejects disallowed file types', function () {
    Storage::fake('public');

    $serviceOrder = ServiceOrder::factory()->create(['type' => 'XRAY']);
    $file = UploadedFile::fake()->create('malware.exe', 10);

    $response = $this->postJson("/api/xray/service-orders/{$serviceOrder->id}/attachments", [
        'file' => $file,
    ]);

    $response->assertUnprocessable();
});
