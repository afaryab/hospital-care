<?php

namespace App\Filament\Accounts\Resources\ExpenseVouchers;

use App\Filament\Accounts\Resources\ExpenseVouchers\Pages\CreateExpenseVoucher;
use App\Filament\Accounts\Resources\ExpenseVouchers\Pages\EditExpenseVoucher;
use App\Filament\Accounts\Resources\ExpenseVouchers\Pages\ListExpenseVouchers;
use App\Filament\Accounts\Resources\ExpenseVouchers\Pages\ViewExpenseVoucher;
use App\Filament\Accounts\Resources\ExpenseVouchers\Schemas\ExpenseVoucherForm;
use App\Filament\Accounts\Resources\ExpenseVouchers\Schemas\ExpenseVoucherInfolist;
use App\Filament\Accounts\Resources\ExpenseVouchers\Tables\ExpenseVouchersTable;
use App\Models\ExpenseVoucher;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ExpenseVoucherResource extends Resource
{
    protected static ?string $model = ExpenseVoucher::class;

    protected static ?string $recordTitleAttribute = 'vc_number';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    public static function form(Schema $schema): Schema
    {
        return ExpenseVoucherForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ExpenseVoucherInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ExpenseVouchersTable::configure($table);
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
            'index' => ListExpenseVouchers::route('/'),
            'create' => CreateExpenseVoucher::route('/create'),
            'view' => ViewExpenseVoucher::route('/{record}'),
            'edit' => EditExpenseVoucher::route('/{record}/edit'),
        ];
    }
}
