<?php

namespace App\Filament\Admin\Resources\StockMovements;

use App\Enum\StockMovementType;
use App\Filament\Admin\Resources\StockMovements\Pages\CreateStockMovement;
use App\Filament\Admin\Resources\StockMovements\Pages\ListStockMovements;
use App\Models\ServiceDepartment;
use App\Models\StockItem;
use App\Models\StockMovement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
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

class StockMovementResource extends Resource
{
    protected static ?string $model = StockMovement::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedArrowsRightLeft;

    protected static string|UnitEnum|null $navigationGroup = 'Inventory';

    protected static ?string $label = 'Stock Movement';

    protected static ?int $navigationSort = 3;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('stock_item_id')
                ->label('Item')
                ->options(fn () => StockItem::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            Select::make('type')
                ->options(collect(StockMovementType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->value]))
                ->required(),
            TextInput::make('quantity')->numeric()->required()->minValue(0.01),
            TextInput::make('unit_cost')->label('Unit Cost (PKR)')->numeric()->minValue(0),
            TextInput::make('batch_number')->label('Batch #')->maxLength(100),
            DatePicker::make('expiry_date')->label('Expiry Date'),
            Select::make('department_id')
                ->label('Department')
                ->options(fn () => ServiceDepartment::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Textarea::make('notes')->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('created_at')->label('Date')->dateTime('d M Y H:i')->sortable(),
                TextColumn::make('stockItem.name')->label('Item')->searchable()->sortable(),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn (string $state) => $state === 'IN' ? 'success' : 'danger'),
                TextColumn::make('quantity')->numeric(2)->sortable(),
                TextColumn::make('unit_cost')->label('Unit Cost')->numeric(2)->sortable()->toggleable(),
                TextColumn::make('batch_number')->label('Batch #')->toggleable(),
                TextColumn::make('expiry_date')->label('Expiry')->date('d M Y')->toggleable(),
                TextColumn::make('department.name')->label('Department')->toggleable(),
                TextColumn::make('movedBy.name')->label('Recorded By')->toggleable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(collect(StockMovementType::cases())->mapWithKeys(fn ($c) => [$c->value => $c->value])),
                SelectFilter::make('stock_item_id')
                    ->label('Item')
                    ->options(fn () => StockItem::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListStockMovements::route('/'),
            'create' => CreateStockMovement::route('/create'),
        ];
    }
}
