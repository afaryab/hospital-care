<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Schemas;

use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\PaymentMethod;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AdministrativeTransactionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Transaction Details')
                ->schema([
                    Select::make('income_or_expense')
                        ->label('Direction')
                        ->options([
                            'EXPENSE' => 'Expense',
                            'INCOME' => 'Income',
                        ])
                        ->default('EXPENSE')
                        ->required()
                        ->live(),

                    Select::make('expense_category_id')
                        ->label('Expense Category')
                        ->options(fn () => ExpenseCategory::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Get $get): bool => $get('income_or_expense') === 'EXPENSE'),

                    Select::make('patient_id')
                        ->label('Patient (Optional)')
                        ->options(fn () => Patient::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable(),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->minValue(0)
                        ->required(),

                    Textarea::make('notes')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Payment Details')
                ->schema([
                    Select::make('payment_method_id')
                        ->label('Payment Method')
                        ->options(fn () => PaymentMethod::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->required()
                        ->live(),

                    TextInput::make('reference_number')
                        ->label('Reference / ID Number')
                        ->nullable()
                        ->visible(fn (Get $get): bool => (bool) PaymentMethod::find($get('payment_method_id'))?->id_required),

                    Select::make('payable_id')
                        ->label(fn (Get $get): string => match (PaymentMethod::find($get('payment_method_id'))?->payables) {
                            'bank_account' => 'Bank Account',
                            'panel' => 'Panel',
                            default => 'Account',
                        })
                        ->options(function (Get $get): array {
                            $method = PaymentMethod::find($get('payment_method_id'));
                            if (! $method || ! $method->requiresPayable()) {
                                return [];
                            }

                            return match ($method->payables) {
                                'bank_account' => BankAccount::query()->orderBy('name')->pluck('name', 'id')->toArray(),
                                'panel' => Panel::query()->orderBy('name')->pluck('name', 'id')->toArray(),
                                default => [],
                            };
                        })
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Get $get): bool => (bool) PaymentMethod::find($get('payment_method_id'))?->requiresPayable()),
                ])
                ->columns(2),
        ]);
    }
}
