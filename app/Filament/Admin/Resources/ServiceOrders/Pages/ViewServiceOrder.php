<?php

namespace App\Filament\Admin\Resources\ServiceOrders\Pages;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\TransactionElement;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

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
                            ->with(['expCategory', 'payedTo'])
                            ->get()),
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
                ->modalHeading('Adjust Service Order Charges')
                ->modalDescription('Update the amount of each income line. Linked receivables will be recalculated automatically.')
                ->modalWidth('3xl')
                ->fillForm(fn () => [
                    'reason' => null,
                    'elements' => TransactionElement::query()
                        ->where('service_order_id', $this->record->id)
                        ->where('income_or_expense', 'INCOME')
                        ->with('transaction:id,tr_number')
                        ->orderBy('created_at')
                        ->get()
                        ->map(fn (TransactionElement $el) => [
                            'id' => $el->id,
                            'tr_number' => $el->transaction?->tr_number ?? '—',
                            'amount' => (float) $el->amount,
                        ])
                        ->all(),
                ])
                ->schema([
                    Repeater::make('elements')
                        ->label('Income Lines')
                        ->schema([
                            TextInput::make('tr_number')
                                ->label('TR#')
                                ->disabled()
                                ->dehydrated(false),
                            TextInput::make('amount')
                                ->label('Amount')
                                ->numeric()
                                ->step('0.01')
                                ->minValue(0)
                                ->required(),
                            TextInput::make('id')
                                ->hidden()
                                ->dehydrated(),
                        ])
                        ->columns(2)
                        ->addable(false)
                        ->deletable(false)
                        ->reorderable(false)
                        ->itemLabel(fn (array $state): ?string => $state['tr_number'] ?? null),
                    TextEntry::make('affected_receivables')
                        ->label('Affected Receivables')
                        ->html()
                        ->state(function (): HtmlString {
                            $receivables = Receaveable::query()
                                ->whereIn('transaction_id', TransactionElement::query()
                                    ->where('service_order_id', $this->record->id)
                                    ->where('income_or_expense', 'INCOME')
                                    ->pluck('transaction_id')
                                    ->unique()
                                    ->filter())
                                ->with(['transaction:id,tr_number', 'panel:id,name'])
                                ->get();

                            if ($receivables->isEmpty()) {
                                return new HtmlString('<p class="text-sm text-gray-500">No receivables linked to this service order.</p>');
                            }

                            $rows = $receivables->map(function (Receaveable $r): string {
                                $original = (float) ($r->orignal_amount ?? $r->amount);
                                $remaining = (float) $r->amount;
                                $paid = max(0.0, $original - $remaining);

                                return sprintf(
                                    '<tr><td class="px-2 py-1 font-mono text-xs">%s</td><td class="px-2 py-1">%s</td><td class="px-2 py-1 text-right">%s</td><td class="px-2 py-1 text-right">%s</td><td class="px-2 py-1 text-right text-amber-700">%s</td><td class="px-2 py-1"><span class="rounded bg-gray-100 px-1.5 py-0.5 text-xs">%s</span></td></tr>',
                                    e($r->transaction?->tr_number ?? '—'),
                                    e($r->panel?->name ?? 'Patient'),
                                    number_format($original, 2),
                                    number_format($paid, 2),
                                    number_format($remaining, 2),
                                    e(strtoupper((string) $r->status))
                                );
                            })->implode('');

                            $html = '<div class="overflow-x-auto"><table class="w-full text-sm border border-gray-200">'
                                .'<thead class="bg-gray-50"><tr>'
                                .'<th class="px-2 py-1 text-left">TR#</th>'
                                .'<th class="px-2 py-1 text-left">Payer</th>'
                                .'<th class="px-2 py-1 text-right">Original</th>'
                                .'<th class="px-2 py-1 text-right">Paid</th>'
                                .'<th class="px-2 py-1 text-right">Remaining</th>'
                                .'<th class="px-2 py-1 text-left">Status</th>'
                                .'</tr></thead><tbody>'.$rows.'</tbody></table></div>'
                                .'<p class="mt-2 text-xs text-gray-500">When you save, each receivable will be re-sized: <code>new_original = new transaction amount</code>, <code>new_remaining = max(0, new_original − amount already paid)</code>. If a receivable becomes fully covered it will be marked <strong>paid</strong>.</p>';

                            return new HtmlString($html);
                        }),
                    Textarea::make('reason')
                        ->label('Reason for Adjustment')
                        ->rows(2)
                        ->maxLength(1000)
                        ->required()
                        ->helperText('Required for audit trail.'),
                ])
                ->action(function (array $data): void {
                    $receivableChanges = [];

                    DB::transaction(function () use ($data, &$receivableChanges) {
                        $touchedTransactionIds = collect();

                        foreach ($data['elements'] ?? [] as $row) {
                            $element = TransactionElement::find($row['id'] ?? null);
                            if (! $element) {
                                continue;
                            }

                            $newAmount = (float) ($row['amount'] ?? 0);
                            if ((float) $element->amount === $newAmount) {
                                continue;
                            }

                            $element->amount = $newAmount;
                            $element->save();

                            if ($element->transaction_id) {
                                $touchedTransactionIds->push($element->transaction_id);
                            }
                        }

                        foreach ($touchedTransactionIds->unique() as $transactionId) {
                            $transaction = Transaction::find($transactionId);
                            if (! $transaction) {
                                continue;
                            }
                            $newTotal = (float) TransactionElement::query()
                                ->where('transaction_id', $transactionId)
                                ->where('income_or_expense', 'INCOME')
                                ->sum('amount');
                            $transaction->amount = $newTotal;
                            $transaction->save();

                            $receivables = Receaveable::query()
                                ->where('transaction_id', $transactionId)
                                ->get();

                            foreach ($receivables as $receivable) {
                                $oldOriginal = (float) ($receivable->orignal_amount ?? $receivable->amount);
                                $oldRemaining = (float) $receivable->amount;
                                $paid = max(0.0, $oldOriginal - $oldRemaining);

                                $newOriginal = $newTotal;
                                $newRemaining = max(0.0, $newOriginal - $paid);

                                $receivable->orignal_amount = $newOriginal;
                                $receivable->amount = $newRemaining;

                                if ($newRemaining <= 0.0 && $newOriginal > 0.0) {
                                    $receivable->status = 'paid';
                                } elseif ($newOriginal <= 0.0) {
                                    $receivable->status = 'cancelled';
                                } elseif (in_array(strtolower((string) $receivable->status), ['paid', 'payed'], true) && $newRemaining > 0.0) {
                                    $receivable->status = 'pending';
                                }

                                $receivable->save();

                                $receivableChanges[] = [
                                    'id' => $receivable->id,
                                    'transaction_id' => $transactionId,
                                    'old' => ['original' => $oldOriginal, 'remaining' => $oldRemaining],
                                    'new' => ['original' => $newOriginal, 'remaining' => $newRemaining, 'status' => $receivable->status],
                                ];
                            }
                        }

                        activity()
                            ->performedOn($this->record)
                            ->causedBy(auth()->user())
                            ->withProperties([
                                'reason' => $data['reason'] ?? null,
                                'adjusted_elements' => collect($data['elements'] ?? [])
                                    ->map(fn ($r) => ['id' => $r['id'] ?? null, 'amount' => $r['amount'] ?? null])
                                    ->all(),
                                'receivable_changes' => $receivableChanges,
                            ])
                            ->log('service_order_charges_adjusted');
                    });

                    $msg = count($receivableChanges) > 0
                        ? sprintf('Charges updated. %d receivable(s) recalculated.', count($receivableChanges))
                        : 'Charges updated.';

                    Notification::make()
                        ->title($msg)
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
}
