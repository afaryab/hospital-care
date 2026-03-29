<?php

namespace App\Filament\Admin\Resources\Incidents\Tables;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
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
                    ->searchable(),
                TextColumn::make('severity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('User')
                    ->default('System')
                    ->searchable(),
                TextColumn::make('patient.ps_number')
                    ->label('Patient')
                    ->default('N/A')
                    ->searchable(),
                TextColumn::make('ip_address')
                    ->label('IP')
                    ->default('N/A')
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'failed_login_threshold' => 'Failed login threshold',
                        'new_login_context' => 'New login context',
                        'bulk_patient_access' => 'Bulk patient access',
                    ]),
                SelectFilter::make('severity')
                    ->options([
                        'critical' => 'Critical',
                        'high' => 'High',
                        'medium' => 'Medium',
                        'low' => 'Low',
                    ]),
                SelectFilter::make('user_id')
                    ->label('User')
                    ->options(fn (): array => User::query()->orderBy('name')->pluck('name', 'id')->toArray())
                    ->searchable(),
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')->label('From'),
                        DatePicker::make('until')->label('Until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('occurred_at', '>=', $date))
                            ->when($data['until'] ?? null, fn (Builder $q, $date): Builder => $q->whereDate('occurred_at', '<=', $date));
                    }),
            ])
            ->defaultSort('occurred_at', 'desc')
            ->recordAction(null)
            ->recordUrl(null);
    }
}
