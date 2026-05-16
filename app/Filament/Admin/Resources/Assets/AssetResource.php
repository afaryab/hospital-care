<?php

namespace App\Filament\Admin\Resources\Assets;

use App\Enum\AssetStatus;
use App\Filament\Admin\Resources\Assets\Pages\CreateAsset;
use App\Filament\Admin\Resources\Assets\Pages\EditAsset;
use App\Filament\Admin\Resources\Assets\Pages\ListAssets;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\ServiceDepartment;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
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

class AssetResource extends Resource
{
    protected static ?string $model = Asset::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedComputerDesktop;

    protected static string|UnitEnum|null $navigationGroup = 'Assets';

    protected static ?string $label = 'Asset';

    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('asset_number')->label('Asset #')->disabled(),
            TextInput::make('name')->required()->maxLength(255)->columnSpan(2),
            Select::make('category_id')
                ->label('Category')
                ->options(fn () => AssetCategory::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->required(),
            TextInput::make('serial_number')->maxLength(255),
            TextInput::make('vendor_name')->label('Vendor')->maxLength(255),
            TextInput::make('purchase_cost')->label('Purchase Cost (PKR)')->numeric(),
            DatePicker::make('purchase_date')->label('Purchase Date'),
            DatePicker::make('warranty_expiry')->label('Warranty Expiry'),
            TextInput::make('location')->maxLength(255),
            Select::make('assigned_to_department_id')
                ->label('Assigned Department')
                ->options(fn () => ServiceDepartment::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Select::make('assigned_to_user_id')
                ->label('Assigned User')
                ->options(fn () => User::orderBy('name')->pluck('name', 'id'))
                ->searchable()
                ->nullable(),
            Select::make('status')
                ->options(collect(AssetStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucwords(str_replace('_', ' ', $c->value))]))
                ->default(AssetStatus::Active->value)
                ->required(),
            Textarea::make('disposal_reason')->label('Disposal Reason')->nullable()->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('asset_number')->label('Asset #')->searchable()->sortable()->fontFamily('mono'),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('category.name')->label('Category')->sortable()->toggleable(),
                TextColumn::make('serial_number')->label('Serial #')->searchable()->toggleable(),
                TextColumn::make('location')->searchable()->toggleable(),
                TextColumn::make('assignedDepartment.name')->label('Department')->toggleable(),
                TextColumn::make('assignedUser.name')->label('Assigned To')->toggleable(),
                TextColumn::make('purchase_cost')->label('Cost (PKR)')->numeric(2)->sortable()->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'active' => 'success',
                        'under_maintenance' => 'warning',
                        'retired', 'disposed' => 'gray',
                        default => 'gray',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(AssetStatus::cases())->mapWithKeys(fn ($c) => [$c->value => ucwords(str_replace('_', ' ', $c->value))])),
                SelectFilter::make('category_id')
                    ->label('Category')
                    ->options(fn () => AssetCategory::orderBy('name')->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])])
            ->striped();
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAssets::route('/'),
            'create' => CreateAsset::route('/create'),
            'edit' => EditAsset::route('/{record}/edit'),
        ];
    }
}
