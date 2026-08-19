<?php

use App\Enum\IncidentSeverity;
use App\Enum\IncidentStatus;
use App\Enum\IncidentType;
use App\Filament\Admin\Resources\Incidents\Pages\CreateIncident;
use App\Filament\Admin\Resources\Incidents\Pages\ListIncidents;
use App\Filament\Admin\Resources\Incidents\Pages\ViewIncident;
use App\Models\Administrator;
use App\Models\Incident;
use App\Models\Receptionist;
use App\Models\User;
use Filament\Actions\Testing\TestAction;

beforeEach(function () {
    $this->user = User::factory()->create();
    Administrator::create(['user_id' => $this->user->id, 'authority' => 'administrator']);
    $this->actingAs($this->user);
});

test('incident list page renders and shows records', function () {
    $incidents = Incident::factory()->count(2)->create();

    Livewire\Livewire::test(ListIncidents::class)
        ->assertSuccessful()
        ->assertCanSeeTableRecords($incidents);
});

test('incident create page renders', function () {
    Livewire\Livewire::test(CreateIncident::class)->assertSuccessful();
});

test('admin can manually report an incident and reported_by is set automatically', function () {
    Livewire\Livewire::test(CreateIncident::class)
        ->fillForm([
            'type' => IncidentType::ClinicalError->value,
            'severity' => IncidentSeverity::Medium->value,
            'occurred_at' => now(),
            'description' => 'Wrong dosage administered, caught before harm.',
        ])
        ->call('create')
        ->assertNotified()
        ->assertRedirect();

    $incident = Incident::where('type', IncidentType::ClinicalError)->first();

    expect($incident)->not->toBeNull()
        ->and($incident->status)->toBe(IncidentStatus::Reported)
        ->and($incident->reported_by)->toBe($this->user->id)
        ->and($incident->context['description'])->toBe('Wrong dosage administered, caught before harm.');
});

test('incident view page renders', function () {
    $incident = Incident::factory()->create();

    Livewire\Livewire::test(ViewIncident::class, ['record' => $incident->getRouteKey()])
        ->assertSuccessful();
});

test('the classify action is only visible for a reported incident', function () {
    $reported = Incident::factory()->create();
    $classified = Incident::factory()->status(IncidentStatus::Classified)->create();

    Livewire\Livewire::test(ListIncidents::class)
        ->assertActionVisible(TestAction::make('classify')->table($reported))
        ->assertActionHidden(TestAction::make('classify')->table($classified));
});

test('classifying an incident advances its status and updates severity', function () {
    $incident = Incident::factory()->create(['severity' => IncidentSeverity::Low]);

    Livewire\Livewire::test(ListIncidents::class)
        ->callAction(TestAction::make('classify')->table($incident), data: ['severity' => IncidentSeverity::Critical->value])
        ->assertNotified();

    $incident->refresh();
    expect($incident->status)->toBe(IncidentStatus::Classified)
        ->and($incident->severity)->toBe(IncidentSeverity::Critical)
        ->and($incident->classified_at)->not->toBeNull();
});

test('the full lifecycle can be walked through the table actions', function () {
    $assignee = User::factory()->create();
    $incident = Incident::factory()->create();

    $table = Livewire\Livewire::test(ListIncidents::class);

    $table->callAction(TestAction::make('classify')->table($incident), data: ['severity' => IncidentSeverity::High->value]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Classified);

    $table->callAction(TestAction::make('assign')->table($incident), data: ['assigned_to' => $assignee->id]);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Assigned)
        ->and($incident->fresh()->assigned_to)->toBe($assignee->id);

    $table->callAction(TestAction::make('investigate')->table($incident), data: ['investigation_notes' => 'Root cause found.']);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Investigated);

    $table->callAction(TestAction::make('resolve')->table($incident), data: ['resolution_notes' => 'Process updated to prevent recurrence.']);
    expect($incident->fresh()->status)->toBe(IncidentStatus::Resolved);

    $table->callAction(TestAction::make('close')->table($incident));
    expect($incident->fresh()->status)->toBe(IncidentStatus::Closed)
        ->and($incident->fresh()->closed_by)->toBe($this->user->id);
});

test('a non-admin cannot see any lifecycle actions', function () {
    $nonAdmin = User::factory()->create();
    Receptionist::create(['user_id' => $nonAdmin->id]);
    $this->actingAs($nonAdmin);

    $incident = Incident::factory()->create();

    Livewire\Livewire::test(ListIncidents::class)
        ->assertActionHidden(TestAction::make('classify')->table($incident));
});
