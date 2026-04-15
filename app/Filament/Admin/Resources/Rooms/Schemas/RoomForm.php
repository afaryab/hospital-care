<?php

namespace App\Filament\Admin\Resources\Rooms\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class RoomForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('ward_id')
                    ->relationship('ward', 'name')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('room_number'),
                Select::make('type')
                    ->required()
                    ->default('general')
                    ->options([
                        'general' => 'General',
                        'private' => 'Private',
                        'semi-private' => 'Semi-Private',
                        'icu' => 'ICU',
                        'isolation' => 'Isolation',
                    ]),
                TextInput::make('capacity')
                    ->required()
                    ->numeric()
                    ->default(1),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->required(),
            ]);
    }
}
