<?php

namespace App\Filament\Admin\Resources\BedAssignments\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class BedAssignmentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ward_id')
                    ->relationship('ward', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('room_id', null)),
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required()
                    ->live()
                    ->afterStateUpdated(fn ($set) => $set('bed_id', null)),
                Select::make('bed_id')
                    ->relationship('bed', 'bed_number', fn ($query, $get) => $query
                        ->where('is_active', true)
                        ->when($get('room_id'), fn ($q, $roomId) => $q->where('room_id', $roomId))
                    )
                    ->required(),
                Select::make('patient_id')
                    ->relationship('patient', 'name')
                    ->searchable()
                    ->required(),
                Select::make('service_order_id')
                    ->relationship('serviceOrder', 'so_number', fn ($query) => $query->where('type', 'IND'))
                    ->searchable()
                    ->required(),
                Select::make('assigned_by')
                    ->relationship('assignedBy', 'name')
                    ->required(),
                DateTimePicker::make('admitted_at')
                    ->required()
                    ->default(now()),
                DateTimePicker::make('discharged_at'),
                Select::make('status')
                    ->required()
                    ->default('active')
                    ->options([
                        'active' => 'Active',
                        'discharged' => 'Discharged',
                        'transferred' => 'Transferred',
                    ]),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }
}
