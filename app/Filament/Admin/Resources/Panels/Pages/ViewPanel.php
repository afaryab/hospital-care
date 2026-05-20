<?php

namespace App\Filament\Admin\Resources\Panels\Pages;

use App\Filament\Admin\Resources\Panels\PanelResource;
use App\Models\Panel;
use App\Models\Receaveable;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ViewEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;

class ViewPanel extends ViewRecord implements HasTable
{
    use InteractsWithTable;

    protected static string $resource = PanelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('recordCheque')
                ->label('Record Cheque')
                ->icon('heroicon-o-document-plus')
                ->schema([
                    Select::make('bank_account_id')
                        ->label('Deposit To (Bank Account)')
                        ->relationship('bankAccount', 'name')
                        ->nullable(),
                    TextInput::make('cheque_number')
                        ->required()
                        ->maxLength(50),
                    TextInput::make('amount')
                        ->required()
                        ->numeric()
                        ->prefix('PKR'),
                    DatePicker::make('due_date')
                        ->label('Cheque Date'),
                    Select::make('linked_receaveable_id')
                        ->label('Link to Pending Receivable')
                        ->options(fn () => Receaveable::where('panel_id', $this->record->id)
                            ->whereIn('status', ['unpaid', 'partial'])
                            ->with('patient')
                            ->get()
                            ->mapWithKeys(fn ($r) => [$r->id => "#{$r->id} - {$r->patient?->name} (PKR {$r->amount})"])
                            ->toArray())
                        ->nullable()
                        ->searchable(),
                ])
                ->action(function (array $data): void {
                    $this->record->panelCheques()->create(array_merge($data, [
                        'status' => 'pending',
                    ]));

                    Notification::make()->title('Cheque recorded.')->success()->send();
                }),
        ];
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('id')->label('#')->sortable(),
            TextColumn::make('patient.name')->label('Patient')->searchable(),
            TextColumn::make('amount')->money('PKR')->sortable(),
            TextColumn::make('orignal_amount')->label('Original')->money('PKR'),
            TextColumn::make('due_date')->date()->sortable(),
            TextColumn::make('status')
                ->badge()
                ->color(fn (string $state) => match ($state) {
                    'paid' => 'success',
                    'partial' => 'warning',
                    'unpaid', 'PENDING' => 'danger',
                    default => 'gray',
                }),
            TextColumn::make('created_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
        ];
    }

    public function table(Tables\Table $table): Tables\Table
    {
        return $table
            ->query(fn () => Receaveable::query()->where('panel_id', $this->record->id))
            ->columns($this->getTableColumns())
            ->defaultSort('created_at', 'desc');
    }

    public function getContentTabLabel(): ?string
    {
        return null;
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Tabs')
                ->tabs([
                    Tab::make('Pending Receivables')
                        ->schema([
                            ViewEntry::make('pending_receivables')
                                ->label(false)
                                ->view('filament.admin.panels.infolists.pending-receivables')
                                ->viewData(fn (Panel $record) => [
                                    'panel' => $record,
                                    'receivables' => $record->receaveables()
                                        ->whereIn('status', ['unpaid', 'partial', 'PENDING'])
                                        ->with('patient')
                                        ->latest()
                                        ->paginate(20),
                                ])
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Paid')
                        ->schema([
                            ViewEntry::make('paid_receivables')
                                ->label(false)
                                ->view('filament.admin.panels.infolists.paid-receivables')
                                ->viewData(fn (Panel $record) => [
                                    'panel' => $record,
                                    'receivables' => $record->receaveables()
                                        ->where('status', 'paid')
                                        ->with('patient')
                                        ->latest()
                                        ->paginate(20),
                                ])
                                ->columnSpanFull(),
                        ]),
                    Tab::make('Cheques')
                        ->schema([
                            ViewEntry::make('cheques')
                                ->label(false)
                                ->view('filament.admin.panels.infolists.cheques')
                                ->viewData(fn (Panel $record) => [
                                    'panel' => $record,
                                    'cheques' => $record->panelCheques()->with('bankAccount')->latest()->paginate(20),
                                ])
                                ->columnSpanFull(),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }
}
