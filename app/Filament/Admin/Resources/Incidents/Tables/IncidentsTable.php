<?php

namespace App\Filament\Admin\Resources\Incidents\Tables;

use App\Enum\IncidentSeverity;
use App\Enum\IncidentStatus;
use App\Enum\IncidentType;
use App\Filament\Admin\Resources\Incidents\Pages\ViewIncident;
use App\Models\Incident;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class IncidentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('occurred_at')
                    ->label('Date / Time')
                    ->dateTime('d M Y, H:i:s')
                    ->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (IncidentType $state): string => $state->label())
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->formatStateUsing(fn (IncidentSeverity $state): string => $state->label())
                    ->color(fn (IncidentSeverity $state): string => $state->color())
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (IncidentStatus $state): string => $state->label())
                    ->color(fn (IncidentStatus $state): string => $state->color())
                    ->searchable(),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->default('N/A')
                    ->toggleable(),
                TextColumn::make('assignedTo.name')
                    ->label('Assigned To')
                    ->default('Unassigned')
                    ->toggleable(),
                TextColumn::make('user.name')
                    ->label('Subject')
                    ->default('System')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('patient.ps_number')
                    ->label('Patient')
                    ->default('N/A')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->default('N/A')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(collect(IncidentType::cases())->mapWithKeys(fn (IncidentType $t) => [$t->value => $t->label()])),
                SelectFilter::make('severity')
                    ->options(collect(IncidentSeverity::cases())->mapWithKeys(fn (IncidentSeverity $s) => [$s->value => $s->label()])),
                SelectFilter::make('status')
                    ->options(collect(IncidentStatus::cases())->mapWithKeys(fn (IncidentStatus $s) => [$s->value => $s->label()])),
                SelectFilter::make('user_id')
                    ->label('Subject')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),
                Filter::make('date_range')
                    ->schema([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('occurred_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('occurred_at', '<=', $date));
                    }),
            ])
            ->recordActions([
                Action::make('classify')
                    ->label('Classify')
                    ->icon('heroicon-m-tag')
                    ->color('warning')
                    ->visible(fn (Incident $record): bool => auth()->user()->can('update', $record) && $record->status === IncidentStatus::Reported)
                    ->schema([
                        Select::make('severity')
                            ->options(collect(IncidentSeverity::cases())->mapWithKeys(fn (IncidentSeverity $s) => [$s->value => $s->label()]))
                            ->default(fn (Incident $record) => $record->severity->value)
                            ->required(),
                    ])
                    ->action(function (array $data, Incident $record): void {
                        $record->update([
                            'severity' => $data['severity'],
                            'status' => IncidentStatus::Classified,
                            'classified_at' => now(),
                        ]);

                        Notification::make()->title('Incident classified')->success()->send();
                    })
                    ->modalHeading('Classify Incident')
                    ->requiresConfirmation(),

                Action::make('assign')
                    ->label('Assign')
                    ->icon('heroicon-m-user-plus')
                    ->color('info')
                    ->visible(fn (Incident $record): bool => auth()->user()->can('update', $record) && $record->status === IncidentStatus::Classified)
                    ->schema([
                        Select::make('assigned_to')
                            ->label('Assign To')
                            ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data, Incident $record): void {
                        $record->update([
                            'assigned_to' => $data['assigned_to'],
                            'status' => IncidentStatus::Assigned,
                            'assigned_at' => now(),
                        ]);

                        Notification::make()->title('Incident assigned')->success()->send();
                    })
                    ->modalHeading('Assign Incident')
                    ->requiresConfirmation(),

                Action::make('investigate')
                    ->label('Complete Investigation')
                    ->icon('heroicon-m-magnifying-glass')
                    ->color('primary')
                    ->visible(fn (Incident $record): bool => auth()->user()->can('update', $record) && $record->status === IncidentStatus::Assigned)
                    ->schema([
                        Textarea::make('investigation_notes')
                            ->label('Investigation Findings')
                            ->required(),
                    ])
                    ->action(function (array $data, Incident $record): void {
                        $record->update([
                            'investigation_notes' => $data['investigation_notes'],
                            'status' => IncidentStatus::Investigated,
                            'investigated_at' => now(),
                        ]);

                        Notification::make()->title('Investigation recorded')->success()->send();
                    })
                    ->modalHeading('Record Investigation Findings')
                    ->requiresConfirmation(),

                Action::make('resolve')
                    ->label('Resolve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Incident $record): bool => auth()->user()->can('update', $record) && $record->status === IncidentStatus::Investigated)
                    ->schema([
                        Textarea::make('resolution_notes')
                            ->label('Resolution')
                            ->required(),
                    ])
                    ->action(function (array $data, Incident $record): void {
                        $record->update([
                            'resolution_notes' => $data['resolution_notes'],
                            'status' => IncidentStatus::Resolved,
                            'resolved_at' => now(),
                        ]);

                        Notification::make()->title('Incident resolved')->success()->send();
                    })
                    ->modalHeading('Resolve Incident')
                    ->requiresConfirmation(),

                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-m-lock-closed')
                    ->color('gray')
                    ->visible(fn (Incident $record): bool => auth()->user()->can('update', $record) && $record->status === IncidentStatus::Resolved)
                    ->action(function (Incident $record): void {
                        $record->update([
                            'status' => IncidentStatus::Closed,
                            'closed_at' => now(),
                            'closed_by' => auth()->id(),
                        ]);

                        Notification::make()->title('Incident closed')->success()->send();
                    })
                    ->modalHeading('Close Incident')
                    ->modalDescription('This is the final stage — the incident cannot be reopened.')
                    ->requiresConfirmation(),
            ])
            ->recordUrl(fn (Incident $record) => ViewIncident::getUrl([$record->id]))
            ->defaultSort('occurred_at', 'desc');
    }
}
