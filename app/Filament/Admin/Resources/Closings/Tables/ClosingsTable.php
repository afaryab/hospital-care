<?php

namespace App\Filament\Admin\Resources\Closings\Tables;

use App\Enum\CounterStatus;
use App\Models\Closing;
use App\Services\AbacusClosingService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ClosingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('old_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('ct_number')
                    ->label('CT Information')
                    ->formatStateUsing(function ($record) {
                        $ctNumber = $record->ct_number ?? 'N/A';
                        $receptionName = $record->reception?->name ?? 'Unknown Reception';
                        $status = $record->status ?? 'Unknown';

                        return "CT: {$ctNumber}<br>Reception: {$receptionName}<br>Status: {$status}";
                    })
                    ->html()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('closing_amount')
                    ->label('Opening / Closing')
                    ->formatStateUsing(function (Closing $record) {
                        $opening = number_format((float) ($record->opening_amount ?? 0), 2);
                        $closing = number_format((float) ($record->closing_amount ?? 0), 2);

                        return "Opening: {$opening}<br>Closing: {$closing}";
                    })
                    ->html(),
                TextColumn::make('expense_payed')
                    ->label('Payment Breakdown')
                    ->formatStateUsing(function (Closing $record) {
                        $cash = number_format((float) ($record->closing_amount_cash ?? 0), 2);
                        $cheque = number_format((float) ($record->closing_amount_cheque ?? 0), 2);
                        $card = number_format((float) ($record->closing_amount_card ?? 0), 2);
                        $expense = number_format((float) ($record->expense_payed ?? 0), 2);
                        $received = $record->amount_received !== null
                            ? number_format((float) $record->amount_received, 2)
                            : 'Pending';

                        return "Cash: {$cash}<br>Cheque: {$cheque}<br>Card: {$card}<br>Expenses: {$expense}<br>Received: {$received}";
                    })
                    ->html(),
                TextColumn::make('cash_recieving_time')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('closed_at')
                    ->dateTime()
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
            ->modifyQueryUsing(fn (Builder $query) => $query->with('reception'))
            ->defaultSort('id', 'desc')
            ->filters([
                SelectFilter::make('reception_id')
                    ->label('Reception')
                    ->relationship('reception', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('All Receptions')
                    ->columnSpanFull(),
                SelectFilter::make('receptionist_id')
                    ->label('Receptionist')
                    ->relationship('receptionist', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('All Receptionists')
                    ->columnSpanFull(),
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('report')
                    ->label('Report & Receive')
                    ->icon('heroicon-m-banknotes')
                    ->color('success')
                    ->visible(fn (Closing $record) => $record->status === 'CLOSED')
                    ->schema([
                        TextInput::make('amount_received')
                            ->label('Amount Received')
                            ->numeric()
                            ->required()
                            ->minValue(0)
                            ->default(fn (Closing $record) => $record->amount_received)
                            ->helperText('Confirm the amount of cash collected for this closing.'),
                    ])
                    ->action(function (array $data, Closing $record) {
                        $record->update([
                            'amount_received' => $data['amount_received'],
                            'status' => CounterStatus::REPORTED,
                            'cash_recieving_time' => now(),
                            'reported_by' => auth()->id(),
                        ]);

                        if (AbacusClosingService::isAutoMapEnabled()) {
                            try {
                                $service = new AbacusClosingService;
                                $service->createEntriesForClosing($record);
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title('Abacus mapping failed')
                                    ->body($e->getMessage())
                                    ->warning()
                                    ->send();
                            }
                        }

                        Notification::make()
                            ->title('Closing reported')
                            ->body("Closing {$record->ct_number} marked as REPORTED.")
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Report Closing')
                    ->modalDescription('Confirm the amount received for this closing. Status will change to REPORTED.')
                    ->modalSubmitActionLabel('Report Closing')
                    ->requiresConfirmation(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
