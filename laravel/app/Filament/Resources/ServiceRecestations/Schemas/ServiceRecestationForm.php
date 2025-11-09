<?php

namespace App\Filament\Resources\ServiceRecestations\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ServiceRecestationForm
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
                TextInput::make('created_by')
                    ->required()
                    ->numeric(),
            ]);
    }
}
