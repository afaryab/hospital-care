<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('service_department_id')
                    ->required()
                    ->numeric(),
                TextInput::make('charges')
                    ->required()
                    ->numeric(),
                TextInput::make('charges_include_tax')
                    ->required()
                    ->numeric(),
                TextInput::make('tax_rate')
                    ->required()
                    ->numeric(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('have_service_provider')
                    ->required()
                    ->numeric(),
                TextInput::make('service_provider_types'),
                TextInput::make('is_composit_service')
                    ->required()
                    ->numeric(),
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
            ]);
    }
}
