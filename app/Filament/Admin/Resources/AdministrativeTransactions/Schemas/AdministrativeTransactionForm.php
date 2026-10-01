<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions\Schemas;

use App\Models\BankAccount;
use App\Models\ExpenseCategory;
use App\Models\Panel;
use App\Models\Patient;
use App\Models\PaymentMethod;
use App\Models\Receaveable;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class AdministrativeTransactionForm
{
    public const INCOME_GENERAL = 'general';

    public const INCOME_PANEL_RECEIVABLE = 'panel_receivable';

    public static function isPanelReceivable(Get $get): bool
    {
        return $get('income_or_expense') === 'INCOME' && $get('income_type') === self::INCOME_PANEL_RECEIVABLE;
    }

    /**
     * Open receivables of a panel, labelled for the allocation picker.
     *
     * @return array<int, string>
     */
    public static function openPanelReceivableOptions(?int $panelId): array
    {
        if (! $panelId) {
            return [];
        }

        return Receaveable::query()
            ->with('patient:id,name,ps_number')
            ->where('panel_id', $panelId)
            ->where('amount', '>', 0)
            ->whereRaw('LOWER(status) NOT IN (?, ?, ?)', ['paid', 'cancelled', 'draft'])
            ->oldest('id')
            ->get()
            ->mapWithKeys(fn (Receaveable $receivable): array => [
                $receivable->id => sprintf(
                    '#%d · %s (%s) · %s · outstanding %s',
                    $receivable->id,
                    $receivable->patient?->name ?? 'Unknown patient',
                    $receivable->patient?->ps_number ?? '—',
                    $receivable->created_at?->format('d M Y') ?? '',
                    number_format((float) $receivable->amount, 2),
                ),
            ])
            ->all();
    }

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

                    Select::make('income_type')
                        ->label('Income Type')
                        ->options([
                            self::INCOME_GENERAL => 'General income',
                            self::INCOME_PANEL_RECEIVABLE => 'Panel receivable payment',
                        ])
                        ->default(self::INCOME_GENERAL)
                        ->required()
                        ->live()
                        ->dehydrated(false)
                        ->visible(fn (Get $get, string $operation): bool => $operation === 'create' && $get('income_or_expense') === 'INCOME'),

                    Select::make('expense_category_id')
                        ->label('Expense Category')
                        ->options(fn () => ExpenseCategory::query()->orderBy('name')->pluck('name', 'id')->toArray())
                        ->searchable()
                        ->nullable()
                        ->visible(fn (Get $get): bool => $get('income_or_expense') === 'EXPENSE'),

                    Select::make('patient_id')
                        ->label('Patient (Optional)')
                        ->searchable()
                        ->getSearchResultsUsing(fn (string $search): array => Patient::query()
                            ->where('name', 'like', "%{$search}%")
                            ->limit(30)
                            ->pluck('name', 'id')
                            ->toArray())
                        ->getOptionLabelUsing(fn ($value): ?string => Patient::find($value)?->name)
                        ->nullable()
                        ->hidden(fn (Get $get): bool => self::isPanelReceivable($get)),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->minValue(0)
                        ->required()
                        ->hidden(fn (Get $get): bool => self::isPanelReceivable($get)),

                    Textarea::make('notes')
                        ->nullable()
                        ->columnSpanFull(),
                ])
                ->columns(2),

            Section::make('Panel Receivables')
                ->description('Choose the panel, then each receivable this payment settles and how much goes to it.')
                ->visible(fn (Get $get): bool => self::isPanelReceivable($get))
                ->schema([
                    Select::make('panel_id')
                        ->label('Panel')
                        ->options(fn (): array => Panel::cachedActive()->pluck('name', 'id')->all())
                        ->searchable()
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (callable $set) => $set('allocations', [])),

                    Repeater::make('allocations')
                        ->label('Receivables to settle')
                        ->schema([
                            Select::make('receaveable_id')
                                ->label('Receivable')
                                ->options(fn (Get $get): array => self::openPanelReceivableOptions((int) $get('../../panel_id') ?: null))
                                ->searchable()
                                ->required()
                                ->live()
                                ->distinct()
                                ->disableOptionsWhenSelectedInSiblingRepeaterItems()
                                ->columnSpan(3),
                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->required()
                                ->minValue(0.01)
                                ->maxValue(fn (Get $get): ?float => ($id = $get('receaveable_id')) ? (float) Receaveable::find($id)?->amount : null)
                                ->live(onBlur: true),
                        ])
                        ->columns(4)
                        ->minItems(1)
                        ->required()
                        ->addActionLabel('Add receivable')
                        ->disabled(fn (Get $get): bool => blank($get('panel_id'))),

                    Text::make(fn (Get $get): string => 'Total received: '.number_format(
                        collect($get('allocations') ?? [])->sum(fn ($row): float => (float) ($row['amount'] ?? 0)),
                        2,
                    )),
                ]),

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
