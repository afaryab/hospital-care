<?php

namespace App\Filament\Admin\Resources\PurchaseOrders;

use App\Enum\PurchaseOrderStatus;
use App\Filament\Admin\Resources\PurchaseOrders\Pages\CreatePurchaseOrder;
use App\Filament\Admin\Resources\PurchaseOrders\Pages\EditPurchaseOrder;
use App\Filament\Admin\Resources\PurchaseOrders\Pages\ListPurchaseOrders;
use App\Models\PurchaseOrder;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class PurchaseOrderResource extends Resource
{
    protected static ?string $model = PurchaseOrder::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedShoppingCart;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $label = 'Purchase Order';

    protected static ?int $navigationSort = 4;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('po_number')->label('PO#')->disabled(),
            TextInput::make('vendor_name')->label('Vendor')->required()->maxLength(255),
            Select::make('status')
                ->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)]))
                ->default(PurchaseOrderStatus::Draft->value)
                ->required(),
            TextInput::make('total_amount')->label('Total (PKR)')->numeric()->minValue(0),
            Select::make('approved_by')
                ->label('Approved By')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            DateTimePicker::make('approved_at')->label('Approved At')->nullable(),
            DateTimePicker::make('received_at')->label('Received At')->nullable(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('po_number')->label('PO#')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('vendor_name')->label('Vendor')->searchable()->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'approved' => 'success',
                        'draft' => 'gray',
                        'received' => 'info',
                        'cancelled' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('total_amount')->label('Total (PKR)')->numeric(2)->sortable(),
                TextColumn::make('approvedBy.name')->label('Approved By')->toggleable(),
                TextColumn::make('approved_at')->label('Approved')->dateTime('d M Y')->toggleable(),
                TextColumn::make('received_at')->label('Received')->dateTime('d M Y')->toggleable(),
                TextColumn::make('created_at')->label('Created')->date('d M Y')->sortable()->toggleable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(PurchaseOrderStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucfirst($c->value)])),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPurchaseOrders::route('/'),
            'create' => CreatePurchaseOrder::route('/create'),
            'edit' => EditPurchaseOrder::route('/{record}/edit'),
        ];
    }
}
