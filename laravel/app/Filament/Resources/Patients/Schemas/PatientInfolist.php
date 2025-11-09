<?php

namespace App\Filament\Resources\Patients\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class PatientInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('ps_number'),
                TextEntry::make('name'),
                TextEntry::make('gender')
                    ->placeholder('-'),
                TextEntry::make('age_group')
                    ->placeholder('-'),
                TextEntry::make('age_days')
                    ->placeholder('-'),
                TextEntry::make('age_dob')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('guardian')
                    ->placeholder('-'),
                TextEntry::make('relation')
                    ->placeholder('-'),
                TextEntry::make('contact')
                    ->placeholder('-'),
                TextEntry::make('cnic')
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }
}
