<?php

namespace App\Filament\Admin\Resources\Patients\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PatientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('gender')
                ->options([
                    'm' => 'Male',
                    'f' => 'Female',
                    't' => 'Transgender',
                    'o' => 'Other',
                ])
                ->required(),
            Select::make('age_group')
                ->options([
                    'child' => 'Child',
                    'adult' => 'Adult',
                    'elderly' => 'Elderly',
                ])
                ->nullable(),
            DatePicker::make('age_dob')
                ->label('Date of Birth')
                ->nullable(),
            TextInput::make('contact')
                ->label('Contact')
                ->tel()
                ->nullable(),
            TextInput::make('cnic')
                ->label('CNIC')
                ->nullable(),
            TextInput::make('address')
                ->label('Address')
                ->nullable(),
            TextInput::make('guardian')
                ->nullable(),
            TextInput::make('relation')
                ->nullable(),
        ]);
    }
}
