<?php

namespace App\Filament\Admin\Resources\BankTransactions;

use App\Filament\Admin\Resources\BankTransactions\Pages\CreateBankTransaction;
use App\Filament\Admin\Resources\BankTransactions\Pages\EditBankTransaction;
use App\Filament\Admin\Resources\BankTransactions\Pages\ListBankTransactions;
use App\Filament\Admin\Resources\BankTransactions\Schemas\BankTransactionForm;
use App\Filament\Admin\Resources\BankTransactions\Tables\BankTransactionsTable;
use App\Models\BankTransaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class BankTransactionResource extends Resource
{
    protected static ?string $model = BankTransaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?int $navigationSort = 10;

    public static function form(Schema $schema): Schema
    {
        return BankTransactionForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return BankTransactionsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListBankTransactions::route('/'),
            'create' => CreateBankTransaction::route('/create'),
            'edit' => EditBankTransaction::route('/{record}/edit'),
        ];
    }
}
