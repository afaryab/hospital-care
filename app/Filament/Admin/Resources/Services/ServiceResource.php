<?php

namespace App\Filament\Admin\Resources\Services;

use App\Filament\Admin\Resources\Services\Pages\ManageServices;
use App\Helpers\HealthIconHelper;
use App\Models\Dentist;
use App\Models\EmergencyDoctor;
use App\Models\IndDoctor;
use App\Models\OpdDoctor;
use App\Models\Service;
use App\Models\UltrasoundDoctor;
use App\Models\XrayTechnician;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;
use UnitEnum;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;

    protected static ?int $navigationSort = 3;

    // protected static string | Htmlable | null $navigationBadgeTooltip = Service::count();

    protected static string|UnitEnum|null $navigationGroup = 'Services';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-star';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('icon')
                    ->label('Health Icon')
                    ->options(HealthIconHelper::options())
                    ->searchable()
                    ->helperText('Choose a Health Icons identifier for this service.'),
                TextEntry::make('icon_preview')
                    ->label('Icon Preview')
                    ->state(function (Get $get): HtmlString {
                        $icon = $get('icon');

                        if (blank($icon)) {
                            return new HtmlString('<span class="text-sm text-gray-500">No icon selected.</span>');
                        }

                        return new HtmlString(HealthIconHelper::img($icon).' <span class="ml-2 text-sm">'.$icon.'</span>');
                    })
                    ->html(),
                Select::make('service_department_id')
                    ->relationship('department', 'name')
                    ->required(),
                TextInput::make('charges')
                    ->required()
                    ->numeric(),
                // \Filament\Forms\Components\Toggle::make('charges_include_tax')
                //     ->required()
                //     ->default(false),
                // TextInput::make('tax_rate')
                //     ->required()
                //     ->numeric(),
                TextInput::make('slug')
                    ->required(),
                Toggle::make('is_composit_service')
                    ->default(false),
                Toggle::make('have_service_provider')
                    ->live()
                    ->default(false),
                Select::make('service_provider_types')
                    ->multiple()
                    ->options([
                        OpdDoctor::class => 'OPD Doctor',
                        EmergencyDoctor::class => 'Emergency Doctor',
                        IndDoctor::class => 'Ind Doctor',
                        Dentist::class => 'Dentist',
                        XrayTechnician::class => 'Xray Technician',
                        UltrasoundDoctor::class => 'Ultrasound Doctor',
                    ])
                    ->visible(fn ($get) => $get('have_service_provider')),
                TextInput::make('created_by')
                    ->default(request()->user()?->id)
                    ->hidden()
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icon')
                    ->label('Icon')
                    ->formatStateUsing(function (?string $state) {
                        if (blank($state)) {
                            return 'N/A';
                        }

                        return HealthIconHelper::img($state, 'w-6 h-6').' <span class="ml-1 text-xs">'.$state.'</span>';
                    })
                    ->html()
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable()
                    ->description(fn ($record) => $record->slug),
                TextColumn::make('department.name')
                    ->label('Department')
                    ->description(fn ($record) => $record->department->slug)
                    ->sortable(),
                TextColumn::make('charges')
                    ->numeric()
                    ->sortable(),
                // TextColumn::make('tax_info')
                //     ->label('Tax Information')
                //     ->formatStateUsing(function ($record) {
                //         $includesTax = $record->charges_include_tax ? 'Includes Tax' : 'Excludes Tax';
                //         $taxRate = $record->tax_rate ? $record->tax_rate . '%' : 'N/A';
                //         return $includesTax . ' | Rate: ' . $taxRate;
                //     })
                //     ->sortable(false),
                TextColumn::make('have_service_provider')
                    ->label('Features')
                    ->formatStateUsing(function ($record) {
                        $features = [];
                        if ($record->have_service_provider) {
                            $features[] = '<span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg> Service Provider</span>';
                        }
                        if ($record->is_composit_service) {
                            $features[] = '<span class="inline-flex items-center gap-1"><svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M7 3a1 1 0 000 2h6a1 1 0 100-2H7zM4 7a1 1 0 011-1h10a1 1 0 110 2H5a1 1 0 01-1-1zM2 11a1 1 0 011-1h14a1 1 0 110 2H3a1 1 0 01-1-1z"></path></svg> Composite</span>';
                        }

                        return implode('<br>', $features);
                    })
                    ->html()
                    ->sortable(false),
                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->sortable(),
                TextColumn::make('timestamps')
                    ->label('Created / Updated')
                    ->formatStateUsing(function ($record) {
                        $created = $record->created_at->format('M j, Y g:i A');
                        $updated = $record->updated_at->format('M j, Y g:i A');

                        return "Created: {$created}<br>Updated: {$updated}";
                    })
                    ->html()
                    ->sortable(false)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageServices::route('/'),
        ];
    }
}
