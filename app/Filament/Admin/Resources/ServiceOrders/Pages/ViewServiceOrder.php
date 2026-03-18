<?php

namespace App\Filament\Admin\Resources\ServiceOrders\Pages;

use App\Filament\Admin\Resources\ServiceOrders\ServiceOrderResource;
use App\Models\Receaveable;
use App\Models\Transaction;
use App\Models\TransactionElement;
use Filament\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Components\Section;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewServiceOrder extends ViewRecord
{
    protected static string $resource = ServiceOrderResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Service Order')
                ->schema([
                    TextEntry::make('so_number')->label('SO#')->copyable(),
                    TextEntry::make('patient.name')->label('Patient'),
                    TextEntry::make('service.name')->label('Service'),
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
                ->columns(4),

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
            Action::make('print_pdf')
                ->label('Print PDF')
                ->icon('heroicon-o-printer')
                ->url(fn () => route('reports.generic.service-order', ['id' => $this->record->id]))
                ->openUrlInNewTab(),
        ];
    }
}
