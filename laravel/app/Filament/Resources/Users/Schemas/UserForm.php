<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
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
            ]);
    }
}
