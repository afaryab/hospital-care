<?php

namespace App\Filament\Admin\Resources\Appointments\Pages;

use App\Filament\Admin\Resources\Appointments\AppointmentResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewAppointment extends ViewRecord
{
    protected static string $resource = AppointmentResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Appointment')
                ->schema([
                    TextEntry::make('appointment_number')->label('Appointment #')->copyable(),
                    TextEntry::make('patient.name')->label('Patient'),
                    TextEntry::make('patient.ps_number')->label('MR #'),
                    TextEntry::make('service.name')->label('Service'),
                    TextEntry::make('doctor.name')->label('Doctor')->placeholder('—'),
                    TextEntry::make('scheduled_at')->label('Scheduled')->dateTime('d M Y, h:i A'),
                    TextEntry::make('priority_mode')->badge(),
                    TextEntry::make('status')->badge(),
                    TextEntry::make('checked_in_at')->label('Checked In')->dateTime('d M Y, h:i A')->placeholder('—'),
                    TextEntry::make('cancelled_at')->label('Cancelled')->dateTime('d M Y, h:i A')->placeholder('—'),
                    TextEntry::make('notes')->placeholder('—')->columnSpanFull(),
                ])
                ->columns(2),
            Section::make('Linked Records')
                ->schema([
                    TextEntry::make('serviceOrder.so_number')->label('Reserved Service Order')->placeholder('Not yet materialized'),
                    TextEntry::make('receaveable.status')->label('Draft Receivable Status')->placeholder('None (not Priority mode)'),
                ])
                ->columns(2),
        ]);
    }
}
