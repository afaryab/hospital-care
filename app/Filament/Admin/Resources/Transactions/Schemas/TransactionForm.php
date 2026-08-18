<?php

namespace App\Filament\Admin\Resources\Transactions\Schemas;

use App\Enum\TransactionElementType;
use App\Models\Service;
use App\Models\Transaction;
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
                    Textarea::make('notes')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Line Items')
                ->description('What each service order on this transaction was charged. Editing an amount here changes how much that service order was billed for.')
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
                                ->label('Charged Amount')
                                ->helperText('What the service order was billed for.')
                                ->required()
                                ->numeric()
                                ->minValue(0),
                            TextInput::make('orignal_amount')
                                ->label('Original Amount')
                                ->helperText('Full price before any discount.')
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

            Section::make('Payment')
                ->description(fn (?Transaction $record) => $record?->receaveable_id
                    ? 'This transaction is a payment collected against an existing receivable.'
                    : 'How much the customer paid, and what is recalculated from it on save.')
                ->schema([
                    TextInput::make('amount')
                        ->label(fn (?Transaction $record) => $record?->receaveable_id ? 'Amount Collected' : 'Recognized Amount')
                        ->helperText(fn (?Transaction $record) => $record?->receaveable_id
                            ? 'What was collected in this payment. Applied directly to the linked receivable\'s remaining balance on save.'
                            : 'Amount recognized as revenue: the lesser of total charged and amount paid. Recalculated on save.')
                        ->numeric(2)
                        ->minValue(0)
                        ->required(fn (?Transaction $record) => (bool) $record?->receaveable_id)
                        ->disabled(fn (?Transaction $record) => ! $record?->receaveable_id)
                        ->dehydrated(fn (?Transaction $record) => (bool) $record?->receaveable_id),

                    TextInput::make('customer_payed')
                        ->label('Amount Customer Paid')
                        ->helperText('What the customer handed over. Recognized amount, change, and any outstanding receivable are recalculated from this and the line items above when you save.')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->visible(fn (?Transaction $record) => ! $record?->receaveable_id),

                    TextInput::make('change')
                        ->numeric(2)
                        ->disabled()
                        ->dehydrated(false)
                        ->helperText('Change given back to the customer. Recalculated on save.')
                        ->visible(fn (?Transaction $record) => ! $record?->receaveable_id),
                ])
                ->columns(2)
                ->columnSpanFull(),
        ]);
    }
}
