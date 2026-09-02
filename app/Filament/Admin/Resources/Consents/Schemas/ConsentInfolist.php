<?php

namespace App\Filament\Admin\Resources\Consents\Schemas;

use App\Enum\ConsentMethod;
use App\Enum\ConsentType;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ConsentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Consent Record')
                    ->schema([
                        TextEntry::make('patient.name')
                            ->label('Patient')
                            ->placeholder('N/A'),
                        TextEntry::make('patient.ps_number')
                            ->label('PS Number')
                            ->placeholder('N/A'),
                        TextEntry::make('serviceOrder.so_number')
                            ->label('Service Order')
                            ->placeholder('Not tied to a specific order'),
                        TextEntry::make('consent_type')
                            ->label('Type')
                            ->badge()
                            ->formatStateUsing(fn (ConsentType $state): string => $state->label()),
                        TextEntry::make('consent_method')
                            ->label('Method')
                            ->badge()
                            ->formatStateUsing(fn (ConsentMethod $state): string => $state->label()),
                        TextEntry::make('consented_at')
                            ->label('Consented At')
                            ->dateTime('d M Y, H:i'),
                        TextEntry::make('recordedBy.name')
                            ->label('Recorded By')
                            ->placeholder('N/A'),
                        TextEntry::make('notes')
                            ->placeholder('No additional notes.')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }
}
