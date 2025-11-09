<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('ps_number')
                    ->required(),
                TextInput::make('name')
                    ->required(),
                TextInput::make('gender'),
                TextInput::make('age_group'),
                TextInput::make('age_days'),
                DateTimePicker::make('age_dob'),
                Textarea::make('address')
                    ->columnSpanFull(),
                TextInput::make('guardian'),
                TextInput::make('relation'),
                TextInput::make('contact'),
                TextInput::make('cnic'),
            ]);
    }
}
