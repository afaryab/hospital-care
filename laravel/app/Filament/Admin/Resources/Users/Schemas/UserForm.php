<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('email')
                    ->label('Email address')
                    ->email()
                    ->required(),
                DateTimePicker::make('email_verified_at'),
                TextInput::make('password')
                    ->password()
                    ->required(),
                Textarea::make('two_factor_secret')
                    ->columnSpanFull(),
                Textarea::make('two_factor_recovery_codes')
                    ->columnSpanFull(),
                DateTimePicker::make('two_factor_confirmed_at'),
                DateTimePicker::make('password_expired_at'),
                DateTimePicker::make('last_login'),
                DateTimePicker::make('last_activity'),
                DateTimePicker::make('last_login_attempt'),
                TextInput::make('ip_address'),
                TextInput::make('login_attempts')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('profile_img_path'),
                TextInput::make('profile_img_id')
                    ->numeric(),
                Toggle::make('is_active')
                    ->required(),
                TextInput::make('banned_message'),
                
                Repeater::make('adminProfiles')
                    ->label('Administrator Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'administrator' => 'Administrator',
                                'superadmin' => 'Super Admin'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Administrator Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('accountantProfiles')
                    ->label('Accountant Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'manager' => 'Manager'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Accountant Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('receptionistProfiles')
                    ->label('Receptionist Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'manager' => 'Manager'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Receptionist Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('patientManagerProfiles')
                    ->label('Patient Manager Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('patient_id')
                            ->label('Patient')
                            ->relationship('patient', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                    ])
                    ->addActionLabel('Add Patient Manager Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('opdDoctorProfiles')
                    ->label('OPD Doctor Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'senior' => 'Senior Doctor',
                                'consultant' => 'Consultant'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add OPD Doctor Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('indDoctorProfiles')
                    ->label('Inpatient Doctor Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'senior' => 'Senior Doctor',
                                'consultant' => 'Consultant'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Inpatient Doctor Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('emergencyDoctorProfiles')
                    ->label('Emergency Doctor Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'senior' => 'Senior Doctor',
                                'consultant' => 'Consultant'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Emergency Doctor Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('dentistProfiles')
                    ->label('Dentist Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'senior' => 'Senior Dentist',
                                'consultant' => 'Consultant Dentist'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Dentist Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('ultrasoundDoctorProfiles')
                    ->label('Ultrasound Doctor Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'specialist' => 'Specialist',
                                'consultant' => 'Consultant'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Ultrasound Doctor Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('xrayTechnicianProfiles')
                    ->label('X-Ray Technician Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'senior_technician' => 'Senior Technician',
                                'supervisor' => 'Supervisor'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add X-Ray Technician Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
                
                Repeater::make('nursingStaffProfiles')
                    ->label('Nursing Staff Profiles')
                    ->relationship()
                    ->schema([
                        Select::make('authority')
                            ->options([
                                'assistant' => 'Assistant',
                                'senior_nurse' => 'Senior Nurse',
                                'charge_nurse' => 'Charge Nurse',
                                'head_nurse' => 'Head Nurse'
                            ])
                            ->default('assistant')
                            ->required()
                    ])
                    ->addActionLabel('Add Nursing Staff Profile')
                    ->collapsible()
                    ->collapsed()
                    ->columnSpanFull(),
            ]);
    }
}
