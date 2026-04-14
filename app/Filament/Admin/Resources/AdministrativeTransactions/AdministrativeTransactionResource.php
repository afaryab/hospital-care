<?php

namespace App\Filament\Admin\Resources\AdministrativeTransactions;

use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\CreateAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\EditAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\ListAdministrativeTransactions;
use App\Filament\Admin\Resources\AdministrativeTransactions\Pages\ViewAdministrativeTransaction;
use App\Filament\Admin\Resources\AdministrativeTransactions\Schemas\AdministrativeTransactionForm;
use App\Filament\Admin\Resources\AdministrativeTransactions\Schemas\AdministrativeTransactionInfolist;
use App\Filament\Admin\Resources\AdministrativeTransactions\Tables\AdministrativeTransactionsTable;
use App\Models\Transaction;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class AdministrativeTransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedBanknotes;

    protected static string|\UnitEnum|null $navigationGroup = 'Finance';

    protected static ?string $navigationLabel = 'Administrative Transactions';

    protected static ?string $modelLabel = 'Administrative Transaction';

    protected static ?string $pluralModelLabel = 'Administrative Transactions';

    protected static ?string $slug = 'administrative-transactions';

    protected static ?int $navigationSort = 11;

    public static function form(Schema $schema): Schema
    {
        return AdministrativeTransactionForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AdministrativeTransactionInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AdministrativeTransactionsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAdministrativeTransactions::route('/'),
            'create' => CreateAdministrativeTransaction::route('/create'),
            'view' => ViewAdministrativeTransaction::route('/{record}'),
            'edit' => EditAdministrativeTransaction::route('/{record}/edit'),
        ];
    }
}
