<?php

namespace App\Filament\Admin\Resources\Beds\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class BedForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('room_id')
                    ->relationship('room', 'name')
                    ->required(),
                Select::make('ward_id')
                    ->relationship('ward', 'name')
                    ->required(),
                TextInput::make('bed_number')
                    ->required(),
                Select::make('status')
                    ->required()
                    ->default('available')
                    ->options([
                        'available' => 'Available',
                        'occupied' => 'Occupied',
                        'reserved' => 'Reserved',
                        'maintenance' => 'Under Maintenance',
                        'cleaning' => 'Being Cleaned',
                    ]),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
