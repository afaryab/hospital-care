<?php

namespace App\Filament\Admin\Resources\ServiceRecestations;

use App\Models\Dentist;
use App\Models\EmergencyDoctor;
use App\Models\IndDoctor;
use App\Models\OpdDoctor;
use App\Models\UltrasoundDoctor;
use App\Models\XrayTechnician;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ServiceRecestationForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->maxLength(255),
                Select::make('service_department_id')
                    ->relationship('department', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Select::make('service_id')
                    ->relationship('service', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('charges')
                    ->required()
                    ->numeric(),
                Toggle::make('charges_include_tax')
                    ->default(true),
                TextInput::make('tax_rate')
                    ->required()
                    ->numeric()
                    ->default(0),
                Select::make('color')
                    ->label('Triage / Display Color')
                    ->helperText('Sets the background color when this resuscitation service is listed. Use for emergency triage coding.')
                    ->options([
                        'red' => '🔴 Red — Immediate Resuscitation',
                        'yellow' => '🟡 Yellow — Emergency',
                        'blue' => '🔵 Blue — Urgent',
                        'sky' => '🩵 Sky Blue — Semi Urgent',
                        'green' => '🟢 Green — Non Urgent',
                    ])
                    ->nullable()
                    ->searchable(),
                Toggle::make('have_service_provider')
                    ->live()
                    ->default(false),
                Select::make('service_provider_types')
                    ->multiple()
                    ->options([
                        OpdDoctor::class => 'OPD Doctor',
                        EmergencyDoctor::class => 'Emergency Doctor',
                        IndDoctor::class => 'Ind Doctor',
                        Dentist::class => 'Dentist',
                        XrayTechnician::class => 'Xray Technician',
                        UltrasoundDoctor::class => 'Ultrasound Doctor',
                    ])
                    ->visible(fn ($get) => $get('have_service_provider')),
                TextInput::make('created_by')
                    ->default(request()->user()?->id)
                    ->hidden()
                    ->numeric(),
            ]);
    }
}
