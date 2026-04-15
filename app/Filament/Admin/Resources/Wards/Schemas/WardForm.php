<?php

namespace App\Filament\Admin\Resources\Wards\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class WardForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('type')
                    ->required()
                    ->default('general')
                    ->options([
                        'general' => 'General',
                        'surgical' => 'Surgical',
                        'icu' => 'ICU',
                        'maternity' => 'Maternity',
                        'pediatric' => 'Pediatric',
                        'isolation' => 'Isolation',
                        'other' => 'Other',
                    ]),
                TextInput::make('floor')
                    ->placeholder('e.g. Ground, 1st, 2nd')
                    ->maxLength(50),
                TextInput::make('building')
                    ->maxLength(100),
                Textarea::make('notes')
                    ->columnSpanFull(),
                Toggle::make('is_active')
                    ->default(true)
                    ->required(),
            ]);
    }
}
