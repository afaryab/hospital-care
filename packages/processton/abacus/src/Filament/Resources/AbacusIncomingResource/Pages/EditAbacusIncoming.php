<?php

namespace Processton\Abacus\Filament\Resources\AbacusIncomingResource\Pages;

use BackedEnum;
use Filament\Actions;
use Filament\Forms;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Schema;
use Processton\Abacus\Filament\Resources\AbacusIncomingResource;
use Processton\Abacus\Models\AbacusChartOfAccount;
use Processton\Abacus\Models\AbacusTransaction;
use Processton\Abacus\Models\AbacusYear;
use Processton\Abacus\Models\Currency;

class EditAbacusIncoming extends EditRecord
{
    protected static string $resource = AbacusIncomingResource::class;

    protected static ?string $title = 'Convert Incoming Transaction';

    protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-arrow-right-circle';

    protected static ?string $breadcrumb = 'Transaction Conversion';

    protected function getHeaderActions(): array
    {
        return [
            // Actions\DeleteAction::make(),
        ];
    }

    public function form(Schema $form): Schema
    {
        $currency = Currency::find(config('org.primary_currency'));

        return $form
            ->schema([
                Grid::make(2)->schema([
                    Fieldset::make('Incoming')->schema([
                        Forms\Components\TextInput::make('reference')
                            ->label('Reference')
                            ->disabled()
                            ->columnSpanFull()
                            ->maxLength(255),
                        Forms\Components\DatePicker::make('date')
                            ->label('Date')
                            ->disabled()
                            ->columnSpanFull()
                            ->default(now()),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->maxLength(500)
                            ->disabled()
                            ->columnSpanFull()
                            ->nullable(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount')
                            ->disabled()
                            ->numeric()
                            ->columnSpanFull()
                            ->minValue(0),
                    ]),
                    Fieldset::make('Transactions')->schema([
                        Forms\Components\Repeater::make('transactions')
                            ->schema([
                                Forms\Components\DatePicker::make('date')
                                    ->label('Date')
                                    ->required()
                                    ->default(now()),
                                Forms\Components\Select::make('abacus_chart_of_account_id')
                                    ->label('Type')
                                    ->getSearchResultsUsing(fn (string $search) => AbacusChartOfAccount::where('name', 'like', "%{$search}%")->limit(50)->pluck('name', 'id'))
                                    ->getOptionLabelUsing(fn ($value): ?string => AbacusChartOfAccount::find($value)?->name)
                                    ->searchable()
                                    ->required(),
                                Forms\Components\Select::make('entry_type')
                                    ->label('Type')
                                    ->options([
                                        'debit' => 'Debit',
                                        'credit' => 'Credit',
                                    ])
                                    ->required(),
                                Forms\Components\TextInput::make('amount')
                                    ->label('Amount')
                                    ->suffix($currency?->code, true)
                                    ->required()
                                    ->numeric()
                                    ->minValue(0),
                            ])
                            ->required()
                            ->columns(4)
                            ->columnSpanFull(),
                    ]),
                ])->columnSpanFull(),

            ]);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {

        $data['transactions'] = AbacusTransaction::where('abacus_incoming_id', $data['id'])
            ->get()
            ->each(function ($transaction) use (&$data) {
                $data['transactions'][] = [
                    'date' => $transaction->date,
                    'abacus_chart_of_account_id' => $transaction->abacus_chart_of_account_id,
                    'entry_type' => $transaction->entry_type,
                    'amount' => $transaction->amount,
                ];
            });

        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $transactions = $data['transactions'] ?? [];

        // Check if transactions are provided
        if (empty($transactions) || ! is_array($transactions)) {
            Notification::make()
                ->danger()
                ->title('No transactions provided')
                ->body('You must add at least one transaction before saving.')
                ->send();

            $this->halt();
        }

        $credit = 0;
        $debit = 0;

        // Get active year once
        $activeYear = AbacusYear::where('status', 1)->first();

        if (! $activeYear) {
            Notification::make()
                ->danger()
                ->title('No active accounting year found')
                ->body('Please set an active accounting year before saving transactions.')
                ->send();

            $this->halt();
        }

        // Validate and prepare transactions
        foreach ($transactions as $key => $transaction) {
            if (isset($transaction['entry_type']) && $transaction['entry_type'] === 'credit') {
                $credit += $transaction['amount'] ?? 0;
            } elseif (isset($transaction['entry_type']) && $transaction['entry_type'] === 'debit') {
                $debit += $transaction['amount'] ?? 0;
            }
        }

        // Validation checks
        if ($debit != $credit) {
            Notification::make()
                ->warning()
                ->title('Unbalanced Transaction')
                ->body('Total debit amount must equal total credit amount.')
                ->send();

            $this->halt();
        }

        if ($credit != $this->record->amount) {
            Notification::make()
                ->warning()
                ->title('Amount Mismatch')
                ->body('Total credit amount must match the main transaction amount.')
                ->send();

            $this->halt();
        }

        if ($debit != $this->record->amount) {
            Notification::make()
                ->warning()
                ->title('Amount Mismatch')
                ->body('Total debit amount must match the main transaction amount.')
                ->send();

            $this->halt();
        }

        // Store transactions data for later use in afterSave
        $this->transactionsData = $transactions;
        $this->activeYearId = $activeYear->id;

        // Remove transactions from data since it's not fillable in AbacusIncoming
        unset($data['transactions']);

        return $data;
    }

    protected $transactionsData = [];

    protected $activeYearId = null;

    protected function afterSave(): void
    {
        if (! empty($this->transactionsData) && $this->activeYearId) {
            // Delete existing transactions for this incoming record
            $this->record->transactions()->delete();

            // Create new transactions
            foreach ($this->transactionsData as $transactionData) {
                $this->record->transactions()->create([
                    'abacus_chart_of_account_id' => $transactionData['abacus_chart_of_account_id'],
                    'abacus_incoming_id' => $this->record->id, // Link to the current incoming record
                    'abacus_year_id' => $this->activeYearId,
                    'amount' => $transactionData['amount'],
                    'date' => $transactionData['date'],
                    'entry_type' => $transactionData['entry_type'],
                ]);
            }

            Notification::make()
                ->success()
                ->title('Transactions Updated')
                ->body('All transactions have been successfully saved.')
                ->send();
        }
    }
}
