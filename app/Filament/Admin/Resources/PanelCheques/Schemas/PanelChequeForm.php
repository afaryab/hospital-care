<?php

namespace App\Filament\Admin\Resources\PanelCheques\Schemas;

use App\Models\Receaveable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class PanelChequeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('panel_id')
                    ->label('Panel')
                    ->relationship('panel', 'name')
                    ->required()
                    ->live(),
                Select::make('bank_account_id')
                    ->label('Depositing To (Bank Account)')
                    ->relationship('bankAccount', 'name')
                    ->nullable(),
                TextInput::make('cheque_number')
                    ->required()
                    ->maxLength(50),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->prefix('PKR'),
                DatePicker::make('due_date')
                    ->label('Cheque Date / Due Date'),
                Select::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'received' => 'Received',
                        'bounced' => 'Bounced',
                    ])
                    ->required()
                    ->default('pending')
                    ->live(),
                DatePicker::make('received_at')
                    ->label('Received At')
                    ->visible(fn (Get $get) => $get('status') === 'received'),
                Select::make('linked_receaveable_id')
                    ->label('Link to Receivable')
                    ->options(fn (Get $get) => $get('panel_id')
                        ? Receaveable::where('panel_id', $get('panel_id'))
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->with('patient')
                            ->get()
                            ->mapWithKeys(fn ($r) => [$r->id => "#{$r->id} - {$r->patient?->name} (PKR {$r->amount})"])
                            ->toArray()
                        : [])
                    ->nullable()
                    ->searchable(),
                Textarea::make('notes')
                    ->rows(2),
            ]);
    }
}
