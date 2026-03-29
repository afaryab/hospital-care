<?php

namespace App\Filament\Admin\Resources\Receptions;

use App\Filament\Admin\Resources\Receptions\Pages\ManageReceptions;
use App\Models\Reception;
use BackedEnum;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\DB;
use UnitEnum;

class ReceptionResource extends Resource
{
    protected static ?string $model = Reception::class;

    protected static ?int $navigationSort = 4;

    protected static string|UnitEnum|null $navigationGroup = 'Services';

    protected static string|BackedEnum|null $navigationIcon = 'fab-deskpro';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                Select::make('allowed_departments')
                    ->multiple()
                    ->options(function () {
                        return \App\Models\ServiceDepartment::pluck('name', 'slug')->toArray();
                    }),
                Toggle::make('is_allowed_to_pay_voucher'),
                Toggle::make('is_allowed_to_pay_from_petty_cash'),
                Toggle::make('is_cash_allowed'),
                Toggle::make('is_cheques_allowed'),
                Toggle::make('is_card_allowed'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                // TextColumn::make('is_allowed_to_pay_voucher')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('is_allowed_to_pay_from_petty_cash')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('is_cash_allowed')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('is_cheques_allowed')
                //     ->numeric()
                //     ->sortable(),
                // TextColumn::make('is_card_allowed')
                //     ->numeric()
                //     ->sortable(),
                TextColumn::make('closings_count')
                    ->counts('closings')
                    ->label('Closings')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
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
                    BulkAction::make('merge')
                        ->label('Merge Receptions')
                        ->icon('heroicon-m-arrows-pointing-in')
                        ->color('warning')
                        ->schema([
                            Select::make('primary_reception_id')
                                ->label('Keep This Reception (Primary)')
                                ->options(function ($livewire) {
                                    $selectedIds = collect($livewire->selectedTableRecords ?? [])->values();
                                    if ($selectedIds->isEmpty()) {
                                        return [];
                                    }

                                    return Reception::whereIn('id', $selectedIds)
                                        ->pluck('name', 'id')
                                        ->toArray();
                                })
                                ->required()
                                ->helperText('This reception will be kept and all others will be merged into it.'),
                        ])
                        ->action(function (array $data, $records) {
                            $primaryReceptionId = $data['primary_reception_id'];
                            $primaryReception = Reception::find($primaryReceptionId);

                            if (! $primaryReception) {
                                Notification::make()
                                    ->title('Error')
                                    ->body('Primary reception not found.')
                                    ->danger()
                                    ->send();

                                return;
                            }

                            $recordsToMerge = $records->where('id', '!=', $primaryReceptionId);
                            $mergeCount = 0;

                            foreach ($recordsToMerge as $reception) {
                                // Update all related closings to point to the primary reception
                                DB::table('closings')
                                    ->where('reception_id', $reception->id)
                                    ->update(['reception_id' => $primaryReceptionId]);

                                // Update any other related tables that reference reception_id
                                // You may need to add more table updates here based on your schema

                                // Delete the duplicate reception
                                $reception->delete();
                                $mergeCount++;
                            }

                            Notification::make()
                                ->title('Receptions Merged Successfully')
                                ->body("Merged {$mergeCount} receptions into '{$primaryReception->name}'. All related closings have been updated.")
                                ->success()
                                ->send();
                        })
                        ->requiresConfirmation()
                        ->modalHeading('Merge Receptions')
                        ->modalDescription('This will merge the selected receptions into one primary reception. All related closings will be updated to reference the primary reception, and duplicate receptions will be deleted.')
                        ->modalSubmitActionLabel('Merge Receptions'),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageReceptions::route('/'),
        ];
    }
}
