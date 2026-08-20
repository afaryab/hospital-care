<?php

use App\Enum\IncidentSeverity;
use App\Enum\IncidentStatus;
use App\Models\Incident;
use Illuminate\Validation\ValidationException;

test('a new incident starts in the reported stage', function () {
    $incident = Incident::factory()->create();

    expect($incident->status)->toBe(IncidentStatus::Reported);
});

test('an incident can advance one stage at a time through the full lifecycle', function () {
    $incident = Incident::factory()->create();

    $incident->update(['status' => IncidentStatus::Classified, 'severity' => IncidentSeverity::High]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Classified);

    $incident->update(['status' => IncidentStatus::Assigned]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Assigned);

    $incident->update(['status' => IncidentStatus::Investigated]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Investigated);

    $incident->update(['status' => IncidentStatus::Resolved]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Resolved);

    $incident->update(['status' => IncidentStatus::Closed]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Closed);
});

test('an incident cannot skip a lifecycle stage', function () {
    $incident = Incident::factory()->create();

    expect(fn () => $incident->update(['status' => IncidentStatus::Assigned]))
        ->toThrow(ValidationException::class);

    expect($incident->fresh()->status)->toBe(IncidentStatus::Reported);
});

test('an incident cannot move backward through the lifecycle', function () {
    $incident = Incident::factory()->status(IncidentStatus::Assigned)->create();

    expect(fn () => $incident->update(['status' => IncidentStatus::Classified]))
        ->toThrow(ValidationException::class);
});

test('a closed incident cannot transition further', function () {
    $incident = Incident::factory()->status(IncidentStatus::Closed)->create();

    expect(fn () => $incident->update(['status' => IncidentStatus::Reported]))
        ->toThrow(ValidationException::class);
});

test('updating a non-status field does not trigger the lifecycle check', function () {
    $incident = Incident::factory()->create();

    $incident->update(['investigation_notes' => 'Preliminary note']);

    expect($incident->fresh()->investigation_notes)->toBe('Preliminary note')
        ->and($incident->fresh()->status)->toBe(IncidentStatus::Reported);
});
