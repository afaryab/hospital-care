<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Schemas;

use App\Models\ExpenseVoucher;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ExpenseVoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        $isPaid = fn (?ExpenseVoucher $record): bool => $record !== null && $record->status === 'payed';

        return $schema
            ->components([
                Section::make('Expense Voucher')
                    ->description(fn (?ExpenseVoucher $record) => $isPaid($record)
                        ? 'This voucher has been paid and cannot be modified.'
                        : null)
                    ->schema([
                        TextInput::make('vc_number')
                            ->label('Voucher Number')
                            ->disabled()
                            ->dehydrated(false)
                            ->placeholder('Auto-generated on create'),
                        TextInput::make('old_id')
                            ->numeric()
                            ->disabled($isPaid),
                        Select::make('exp_category_id')
                            ->label('Expense Category')
                            ->relationship('expCategory', 'name')
                            ->searchable()
                            ->preload()
                            ->required()
                            ->disabled($isPaid),
                        Select::make('service_order_id')
                            ->label('Service Order')
                            ->relationship('serviceOrder', 'so_number')
                            ->searchable()
                            ->preload()
                            ->disabled($isPaid),
                        Select::make('payed_to')
                            ->label('Paid To (User)')
                            ->relationship('payedTo', 'name')
                            ->searchable()
                            ->preload()
                            ->disabled($isPaid),
                        TextInput::make('payed_to_name')
                            ->label('Paid To Name')
                            ->maxLength(255)
                            ->disabled($isPaid),
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->disabled($isPaid),
                    ]),
            ]);
    }
}
