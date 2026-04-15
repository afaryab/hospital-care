<?php

namespace App\Filament\Admin\Resources\BedAssignments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class BedAssignmentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('admitted_at', 'desc')
            ->columns([
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.ps_number')
                    ->label('MR#')
                    ->searchable(),
                TextColumn::make('ward.name')
                    ->label('Ward')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('room.name')
                    ->label('Room')
                    ->searchable(),
                TextColumn::make('bed.bed_number')
                    ->label('Bed')
                    ->searchable(),
                TextColumn::make('serviceOrder.so_number')
                    ->label('SO Number')
                    ->searchable(),
                TextColumn::make('admitted_at')
                    ->label('Admitted')
                    ->dateTime('d M Y, H:i')
                    ->sortable(),
                TextColumn::make('discharged_at')
                    ->label('Discharged')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->default('—'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'discharged' => 'gray',
                        'transferred' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ]);
    }
}
