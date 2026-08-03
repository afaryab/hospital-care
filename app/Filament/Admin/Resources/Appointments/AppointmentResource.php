<?php

namespace App\Filament\Admin\Resources\Appointments;

use App\Enum\AppointmentStatus;
use App\Filament\Admin\Resources\Appointments\Pages\ListAppointments;
use App\Filament\Admin\Resources\Appointments\Pages\ViewAppointment;
use App\Models\Appointment;
use App\Services\AppointmentService;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class AppointmentResource extends Resource
{
    protected static ?string $model = Appointment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static string|UnitEnum|null $navigationGroup = 'Reports';

    protected static ?string $label = 'Appointment';

    protected static ?string $recordTitleAttribute = 'appointment_number';

    public static function table(Table $table): Table
    {
        return $table
            ->query(
                Appointment::query()->with(['patient:id,name,ps_number', 'service:id,name', 'doctor:id,name'])
            )
            ->defaultSort('scheduled_at', 'desc')
            ->columns([
                TextColumn::make('appointment_number')
                    ->label('Appointment #')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('patient.name')
                    ->label('Patient')
                    ->description(fn (Appointment $record) => $record->patient?->ps_number)
                    ->searchable(),
                TextColumn::make('service.name')
                    ->label('Service')
                    ->searchable(),
                TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->placeholder('—'),
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
                TextColumn::make('priority_mode')
                    ->badge()
                    ->color(fn ($state) => match ($state?->value ?? $state) {
                        'priority' => 'danger',
                        'medium' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn ($state) => match ($state?->value ?? $state) {
                        'checked_in' => 'success',
                        'no_show' => 'danger',
                        'cancelled' => 'gray',
                        default => 'warning',
                    }),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(AppointmentStatus::cases())
                        ->mapWithKeys(fn (AppointmentStatus $s) => [$s->value => ucwords(str_replace('_', ' ', $s->value))])
                        ->toArray()),
            ])
            ->recordActions([
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon(Heroicon::OutlinedXCircle)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Appointment $record) => $record->status === AppointmentStatus::Scheduled)
                    ->action(function (Appointment $record, AppointmentService $appointmentService): void {
                        $appointmentService->cancel($record);

                        Notification::make()
                            ->title('Appointment cancelled')
                            ->success()
                            ->send();
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAppointments::route('/'),
            'view' => ViewAppointment::route('/{record}'),
        ];
    }
}
