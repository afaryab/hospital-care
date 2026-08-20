<?php

namespace App\Filament\Admin\Resources\Incidents\Schemas;

use App\Enum\IncidentSeverity;
use App\Enum\IncidentStatus;
use App\Enum\IncidentType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class IncidentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Incident')
                ->schema([
                    TextEntry::make('type')->formatStateUsing(fn (IncidentType $state): string => $state->label()),
                    TextEntry::make('severity')->badge()
                        ->formatStateUsing(fn (IncidentSeverity $state): string => $state->label())
                        ->color(fn (IncidentSeverity $state): string => $state->color()),
                    TextEntry::make('status')->badge()
                        ->formatStateUsing(fn (IncidentStatus $state): string => $state->label())
                        ->color(fn (IncidentStatus $state): string => $state->color()),
                    TextEntry::make('occurred_at')->dateTime('d M Y, H:i'),
                    TextEntry::make('department.name')->label('Department')->placeholder('N/A'),
                    TextEntry::make('patient.ps_number')->label('Patient')->placeholder('N/A'),
                    TextEntry::make('user.name')->label('Subject')->placeholder('System'),
                    TextEntry::make('reportedBy.name')->label('Reported By')->placeholder('System (automated)'),
                    TextEntry::make('context')->label('Details')->placeholder('N/A')->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Lifecycle')
                ->schema([
                    TextEntry::make('classified_at')->dateTime('d M Y, H:i')->placeholder('Not yet classified'),
                    TextEntry::make('assignedTo.name')->label('Assigned To')->placeholder('Unassigned'),
                    TextEntry::make('assigned_at')->dateTime('d M Y, H:i')->placeholder('Not yet assigned'),
                    TextEntry::make('investigated_at')->dateTime('d M Y, H:i')->placeholder('Not yet investigated'),
                    TextEntry::make('investigation_notes')->placeholder('N/A')->columnSpanFull(),
                    TextEntry::make('resolved_at')->dateTime('d M Y, H:i')->placeholder('Not yet resolved'),
                    TextEntry::make('resolution_notes')->placeholder('N/A')->columnSpanFull(),
                    TextEntry::make('closedBy.name')->label('Closed By')->placeholder('N/A'),
                    TextEntry::make('closed_at')->dateTime('d M Y, H:i')->placeholder('Not yet closed'),
                ])
                ->columns(2),
        ]);
    }
}
