<?php

namespace App\Filament\Admin\Resources\ServiceOrders\Pages;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use App\Models\Receaveable;
use App\Models\ServiceOrder;
use App\Models\Transaction;
use App\Models\TransactionElement;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\DB;

class ViewServiceOrder extends ViewRecord
{
    protected static string $resource = ServiceOrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Service Order')
                ->schema([
                    TextEntry::make('so_number')->label('SO#')->copyable(),
                    TextEntry::make('so_short')->label('Short#')->copyable(),
                    TextEntry::make('patient.name')->label('Patient'),
                    TextEntry::make('service.name')->label('Service'),
                    TextEntry::make('service.charges')
                        ->label('Package Amount')
                        ->numeric(decimalPlaces: 2),
                    TextEntry::make('doctor.name')->label('Provider'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'closed' => 'success',
                            'open' => 'warning',
                            default => 'gray',
                        }),
                    TextEntry::make('created_at')->label('Created')->dateTime('d M Y, H:i'),
                    TextEntry::make('closed_at')->label('Closed At')->dateTime('d M Y, H:i')->placeholder('-'),
                ])
                ->columns(5),

            Section::make('Income Transactions')
                ->schema([
                    ViewEntry::make('income_elements')
                        ->view('filament.admin.resources.service-orders.income-table')
                        ->state(fn ($record) => TransactionElement::where('service_order_id', $record->id)
                            ->where('income_or_expense', 'INCOME')
                            ->with(['transaction', 'patient'])
                            ->orderBy('created_at')
                            ->get()),
                ]),

            Section::make('Recestation Charges')
                ->schema([
                    ViewEntry::make('recestation_elements')
                        ->view('filament.admin.resources.service-orders.recestation-table')
                        ->state(fn ($record) => TransactionElement::where('service_order_id', $record->id)
                            ->where('type', 'RECES-IND')
                            ->with(['transaction', 'patient', 'serviceRecestation'])
                            ->orderBy('created_at')
                            ->get()),
                ]),

            Section::make('Expense Transactions')
                ->schema([
                    ViewEntry::make('expense_elements')
                        ->view('filament.admin.resources.service-orders.expense-table')
                        ->state(fn ($record) => TransactionElement::where('service_order_id', $record->id)
                            ->where('income_or_expense', 'EXPENSE')
                            ->with(['transaction', 'expenseCategory', 'expVoucher'])
                            ->orderBy('created_at')
                            ->get()),
                ]),

            Section::make('Receivables')
                ->schema([
                    ViewEntry::make('receivables')
                        ->view('filament.admin.resources.service-orders.receivables-table')
                        ->state(fn ($record) => Receaveable::whereIn(
                            'transaction_id',
                            TransactionElement::where('service_order_id', $record->id)
                                ->where('income_or_expense', 'INCOME')
                                ->pluck('transaction_id')
                                ->unique()
                                ->filter()
                        )->with(['patient', 'panel', 'transaction'])->get()),
                ]),

            Section::make('Receivable Payments')
                ->schema([
                    ViewEntry::make('receivable_payments')
                        ->view('filament.admin.resources.service-orders.receivable-payments-table')
                        ->state(function ($record) {
                            $transactionIds = TransactionElement::where('service_order_id', $record->id)
                                ->where('income_or_expense', 'INCOME')
                                ->pluck('transaction_id')->unique()->filter();
                            $receivableIds = Receaveable::whereIn('transaction_id', $transactionIds)->pluck('id')->filter();

                            return Transaction::whereIn('receaveable_id', $receivableIds)
                                ->with(['receaveable.transaction'])
                                ->orderBy('created_at')
                                ->get();
                        }),
                ]),

            Section::make('Expense Vouchers')
                ->schema([
                    ViewEntry::make('expense_vouchers')
                        ->view('filament.admin.resources.service-orders.expense-vouchers-table')
                        ->state(fn ($record) => $record->expenseVouchers()
                            ->withCount('serviceOrders')
                            ->with(['expCategory', 'payedTo'])
                            ->get()
                            ->each(fn ($voucher) => $voucher->share_amount = $voucher->amount / max(1, $voucher->service_orders_count))),
                ]),
        ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('edit_charges')
                ->label('Edit Charges')
                ->icon('heroicon-o-banknotes')
                ->color('warning')
                ->visible(fn () => auth()->user()?->isAdmin() || auth()->user()?->isAccountant())
                ->modalHeading('Adjust Charges, Payment & Receivables')
                ->modalDescription('Update what each line was charged and how much the customer paid. Recognized amount and any outstanding receivable are recalculated automatically on save — edit a receivable field directly to override that instead.')
                ->modalWidth('4xl')
                ->fillForm(function (): array {
                    $data = ['reason' => null];

                    foreach (static::touchedTransactions($this->record) as $transaction) {
                        $prefix = "txn_{$transaction->id}";
                        $receivable = $transaction->receaveable;

                        $data["{$prefix}_customer_payed"] = (float) $transaction->customer_payed;

                        foreach ($transaction->elements->where('service_order_id', $this->record->id)->where('income_or_expense', 'INCOME') as $element) {
                            $data["{$prefix}_line_{$element->id}"] = (float) $element->amount;
                        }

                        if ($receivable) {
                            $data["{$prefix}_receivable_amount"] = (float) $receivable->amount;
                            $data["{$prefix}_receivable_status"] = $receivable->status;
                        }
                    }

                    return $data;
                })
                ->schema(function (): array {
                    $transactions = static::touchedTransactions($this->record);

                    if ($transactions->isEmpty()) {
                        return [
                            TextEntry::make('no_transactions')
                                ->label(false)
                                ->state('No income transactions are linked to this service order yet.'),
                        ];
                    }

                    $components = $transactions->map(function (Transaction $transaction) {
                        $prefix = "txn_{$transaction->id}";
                        $receivable = $transaction->receaveable;
                        $lines = $transaction->elements->where('service_order_id', $this->record->id)->where('income_or_expense', 'INCOME');

                        $fields = $lines->map(fn (TransactionElement $element) => TextInput::make("{$prefix}_line_{$element->id}")
                            ->label($lines->count() > 1 ? "Charged Amount (line #{$element->id})" : 'Charged Amount')
                            ->numeric()
                            ->step('0.01')
                            ->minValue(0)
                            ->required())->values()->all();

                        $fields[] = TextInput::make("{$prefix}_customer_payed")
                            ->label('Amount Customer Paid')
                            ->helperText('Applies to the whole transaction — other service orders bought in the same visit share it.')
                            ->numeric()
                            ->step('0.01')
                            ->minValue(0)
                            ->required();

                        if ($receivable) {
                            $collected = max(0.0, (float) ($receivable->orignal_amount ?? $receivable->amount) - (float) $receivable->amount);

                            $fields[] = TextInput::make("{$prefix}_receivable_amount")
                                ->label('Receivable Remaining')
                                ->helperText("Collected so far: {$collected}. Auto-recalculated from paid vs. charged — change it to manually override.")
                                ->numeric()
                                ->step('0.01')
                                ->minValue(0)
                                ->required();

                            $fields[] = Select::make("{$prefix}_receivable_status")
                                ->label('Receivable Status')
                                ->options(['unpaid' => 'Unpaid', 'paid' => 'Paid', 'cancelled' => 'Cancelled'])
                                ->native(false)
                                ->required();
                        }

                        return Section::make("TR# {$transaction->tr_number}")
                            ->schema($fields)
                            ->columns(2);
                    })->values()->all();

                    $components[] = Textarea::make('reason')
                        ->label('Reason for Adjustment')
                        ->rows(2)
                        ->maxLength(1000)
                        ->required()
                        ->helperText('Required for audit trail.');

                    return $components;
                })
                ->action(function (array $data): void {
                    $transactions = static::touchedTransactions($this->record);

                    if ($transactions->isEmpty()) {
                        Notification::make()
                            ->title('Nothing to update.')
                            ->body('No income transactions are linked to this service order.')
                            ->warning()
                            ->send();

                        return;
                    }

                    $elementChanges = [];
                    $paymentChanges = [];
                    $receivableChanges = [];

                    DB::transaction(function () use ($data, $transactions, &$elementChanges, &$paymentChanges, &$receivableChanges): void {
                        foreach ($transactions as $transaction) {
                            $prefix = "txn_{$transaction->id}";

                            foreach ($transaction->elements->where('service_order_id', $this->record->id)->where('income_or_expense', 'INCOME') as $element) {
                                $newAmount = $data["{$prefix}_line_{$element->id}"] ?? null;
                                if ($newAmount === null || (float) $element->amount === (float) $newAmount) {
                                    continue;
                                }

                                $elementChanges[] = ['id' => $element->id, 'old' => (float) $element->amount, 'new' => (float) $newAmount];
                                $element->amount = (float) $newAmount;
                                $element->save();
                            }

                            $newCustomerPayed = (float) ($data["{$prefix}_customer_payed"] ?? $transaction->customer_payed);
                            if ((float) $transaction->customer_payed !== $newCustomerPayed) {
                                $paymentChanges[] = ['transaction_id' => $transaction->id, 'old' => (float) $transaction->customer_payed, 'new' => $newCustomerPayed];
                                $transaction->customer_payed = $newCustomerPayed;
                                $transaction->save();
                            }

                            $transaction->recalculatePayment();

                            // A receivable field is only present in $data when one existed at
                            // modal-open time — an admin override wins over the recalculation
                            // above; a receivable freshly created by recalculatePayment() (e.g.
                            // the edit introduced a new shortfall) has no override to apply.
                            $overrideAmountKey = "{$prefix}_receivable_amount";
                            if (array_key_exists($overrideAmountKey, $data)) {
                                $receivable = $transaction->receaveable()->first();
                                if ($receivable) {
                                    $newAmount = (float) $data[$overrideAmountKey];
                                    $newStatus = $data["{$prefix}_receivable_status"] ?? $receivable->status;

                                    if ((float) $receivable->amount !== $newAmount || $receivable->status !== $newStatus) {
                                        $receivableChanges[] = [
                                            'id' => $receivable->id,
                                            'old' => ['amount' => (float) $receivable->amount, 'status' => $receivable->status],
                                            'new' => ['amount' => $newAmount, 'status' => $newStatus],
                                        ];
                                        $receivable->update(['amount' => $newAmount, 'status' => $newStatus]);
                                    }
                                }
                            }
                        }

                        activity()
                            ->performedOn($this->record)
                            ->causedBy(auth()->user())
                            ->withProperties([
                                'reason' => $data['reason'] ?? null,
                                'element_changes' => $elementChanges,
                                'payment_changes' => $paymentChanges,
                                'receivable_changes' => $receivableChanges,
                            ])
                            ->log('service_order_charges_adjusted');
                    });

                    Notification::make()
                        ->title('Charges, payment, and receivables updated.')
                        ->success()
                        ->send();
                }),
            Action::make('print_pdf')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('reports.generic.service-order', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }

    /**
     * Every transaction that has an INCOME line item linked to this service
     * order — almost always one, but a service order can be paid for across
     * more than one transaction (e.g. panel splits).
     */
    protected static function touchedTransactions(ServiceOrder $serviceOrder): EloquentCollection
    {
        return Transaction::query()
            ->whereIn('id', TransactionElement::query()
                ->where('service_order_id', $serviceOrder->id)
                ->where('income_or_expense', 'INCOME')
                ->pluck('transaction_id')
                ->unique()
                ->filter())
            ->with(['elements', 'receaveable'])
            ->orderBy('created_at')
            ->get();
    }
}
