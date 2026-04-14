<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use App\Enum\TransactionElementType;
use App\Models\Service;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class TransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Transaction Details')
                ->schema([
                    TextInput::make('tr_number')
                        ->label('TR Number')
                        ->disabled()
                        ->dehydrated(false),
                    TextInput::make('amount')
                        ->label('Total Amount')
                        ->numeric(2)
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Calculated from elements.'),
                    Textarea::make('notes')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Line Items')
                ->headerActions([])
                ->schema([
                    Repeater::make('elements')
                        ->relationship()
                        ->schema([
                            Select::make('type')
                                ->options(collect(TransactionElementType::cases())
                                    ->mapWithKeys(fn (TransactionElementType $t) => [$t->name => $t->name]))
                                ->required(),
                            Select::make('income_or_expense')
                                ->options([
                                    'INCOME' => 'Income',
                                    'EXPENSE' => 'Expense',
                                ])
                                ->required(),
                            Select::make('service_id')
                                ->label('Service')
                                ->options(fn () => Service::query()->orderBy('name')->pluck('name', 'id'))
                                ->searchable()
                                ->nullable(),
                            Select::make('doctor_id')
                                ->label('Doctor / Provider')
                                ->options(fn () => User::query()
                                    ->whereHas('opdDoctorProfiles')
                                    ->orWhereHas('indDoctorProfiles')
                                    ->orWhereHas('emergencyDoctorProfiles')
                                    ->orWhereHas('dentistProfiles')
                                    ->orWhereHas('ultrasoundDoctorProfiles')
                                    ->orderBy('name')
                                    ->pluck('name', 'id'))
                                ->searchable()
                                ->nullable(),
                            TextInput::make('amount')
                                ->required()
                                ->numeric()
                                ->minValue(0),
                            TextInput::make('orignal_amount')
                                ->label('Original Amount')
                                ->numeric()
                                ->nullable(),
                            Textarea::make('notes')
                                ->nullable()
                                ->columnSpanFull(),
                        ])
                        ->columns(2)
                        ->columnSpanFull()
                        ->addActionLabel('Add Line Item')
                        ->reorderable(false),
                ])
                ->columnSpanFull(),
        ]);
    }
}
