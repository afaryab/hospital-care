<?php

namespace App\Filament\Admin\Resources\ExpenseVouchers\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ExpenseVoucherForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('vc_number')
                    ->label('Voucher Number')
                    ->disabled()
                    ->dehydrated(false)
                    ->placeholder('Auto-generated on create'),
                TextInput::make('old_id')
                    ->numeric(),
                Select::make('exp_category_id')
                    ->label('Expense Category')
                    ->relationship('expCategory', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('service_order_id')
                    ->label('Service Order')
                    ->relationship('serviceOrder', 'so_number')
                    ->searchable()
                    ->preload(),
                Select::make('payed_to')
                    ->label('Paid To (User)')
                    ->relationship('payedTo', 'name')
                    ->searchable()
                    ->preload(),
                TextInput::make('payed_to_name')
                    ->label('Paid To Name')
                    ->maxLength(255),
                TextInput::make('amount')
                    ->required()
                    ->numeric()
                    ->minValue(0)
                    ->default(0),
            ]);
    }
}
