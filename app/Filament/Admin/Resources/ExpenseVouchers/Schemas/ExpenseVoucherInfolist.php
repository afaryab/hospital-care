<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Schemas;

use App\Models\ExpenseVoucher;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseVoucherInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Expense Voucher Information')
                    ->schema([
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn (string $state): string => $state === 'payed' ? 'success' : 'warning'),
                        TextEntry::make('vc_number')
                            ->label('Voucher Number'),
                        TextEntry::make('expCategory.name')
                            ->label('Expense Category')
                            ->placeholder('N/A'),
                        TextEntry::make('serviceOrder.service.name')
                            ->label('Service')
                            ->placeholder('N/A'),
                        TextEntry::make('payed_to')
                            ->label('Paid To')
                            ->formatStateUsing(function (ExpenseVoucher $record) {
                                return $record->payedTo?->name ?? $record->payed_to_name ?? 'N/A';
                            }),
                        TextEntry::make('amount')
                            ->numeric(decimalPlaces: 2),
                        TextEntry::make('created_at')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->dateTime(),
                    ])
                    ->columns(2),

                Section::make('Payment Transaction')
                    ->schema([
                        TextEntry::make('transaction.tr_number')
                            ->label('Transaction Number'),
                        TextEntry::make('transaction.closing.ct_number')
                            ->label('Closing'),
                        TextEntry::make('transactionElement.amount')
                            ->label('Paid Amount')
                            ->numeric(decimalPlaces: 2),
                        TextEntry::make('transaction.created_at')
                            ->label('Paid At')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->visible(fn (ExpenseVoucher $record): bool => $record->status === 'payed'),
            ]);
    }
}
