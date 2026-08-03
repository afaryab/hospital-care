<?php

namespace App\Filament\Admin\Pages;

use App\Enum\AppointmentPriorityMode;
use App\Helpers\DateHelper;
use App\Models\HospitalSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;

class HospitalSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static ?int $navigationSort = 9;

    protected static ?string $navigationLabel = 'Hospital Settings';

    protected static ?string $title = 'Hospital Settings';

    protected string $view = 'filament.admin.pages.hospital-settings';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'hospital_name' => HospitalSetting::get('hospital_name', config('app.name')),
            'logo' => HospitalSetting::get('hospital_logo'),
            'address' => HospitalSetting::get('hospital_address'),
            'phone' => HospitalSetting::get('hospital_phone'),
            'email' => HospitalSetting::get('hospital_email'),
            'ntn' => HospitalSetting::get('hospital_ntn'),
            'strn' => HospitalSetting::get('hospital_strn'),
            'timezone' => HospitalSetting::get('hospital_timezone', 'Asia/Karachi'),
            'abacus_auto_map_accounts' => (bool) HospitalSetting::get('abacus_auto_map_accounts', false),
            'appointment_priority_mode' => HospitalSetting::get('appointment_priority_mode', AppointmentPriorityMode::Standard->value),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('hospital_name')
                    ->label('Hospital Name')
                    ->required()
                    ->maxLength(255),
                FileUpload::make('logo')
                    ->label('Logo')
                    ->image()
                    ->disk('public')
                    ->directory('hospital-settings')
                    ->visibility('public'),
                TextInput::make('address')
                    ->label('Address')
                    ->maxLength(255),
                TextInput::make('phone')
                    ->label('Phone')
                    ->maxLength(50),
                TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->maxLength(255),
                TextInput::make('ntn')
                    ->label('NTN')
                    ->maxLength(100),
                TextInput::make('strn')
                    ->label('STRN')
                    ->maxLength(100),
                Select::make('timezone')
                    ->label('Timezone (for printed slips & PDFs)')
                    ->helperText('Database stores UTC. PDFs and printed slips render dates in this timezone.')
                    ->searchable()
                    ->required()
                    ->options(collect(\DateTimeZone::listIdentifiers())
                        ->mapWithKeys(fn (string $tz) => [$tz => $tz])
                        ->toArray())
                    ->default('Asia/Karachi'),
                Toggle::make('abacus_auto_map_accounts')
                    ->label('Auto Map Accounts (Abacus)')
                    ->helperText('When enabled, receiving a closing statement will automatically create Abacus accounting entries.'),
                Select::make('appointment_priority_mode')
                    ->label('Appointment Priority Handling')
                    ->helperText('Controls how every booked appointment is treated hospital-wide once its day arrives: Priority guarantees the slot with a draft receivable and tops the queue; Medium reserves a token but hides the patient\'s identity until check-in; Standard is informational only.')
                    ->required()
                    ->native(false)
                    ->options(collect(AppointmentPriorityMode::cases())
                        ->mapWithKeys(fn (AppointmentPriorityMode $mode) => [$mode->value => $mode->label()])
                        ->toArray())
                    ->default(AppointmentPriorityMode::Standard->value),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $state = $this->form->getState();

        HospitalSetting::set('hospital_name', $state['hospital_name'] ?? null);
        HospitalSetting::set('hospital_logo', $state['logo'] ?? null);
        HospitalSetting::set('hospital_address', $state['address'] ?? null);
        HospitalSetting::set('hospital_phone', $state['phone'] ?? null);
        HospitalSetting::set('hospital_email', $state['email'] ?? null);
        HospitalSetting::set('hospital_ntn', $state['ntn'] ?? null);
        HospitalSetting::set('hospital_strn', $state['strn'] ?? null);
        HospitalSetting::set('hospital_timezone', $state['timezone'] ?? 'Asia/Karachi');
        HospitalSetting::set('abacus_auto_map_accounts', $state['abacus_auto_map_accounts'] ?? false);
        HospitalSetting::set('appointment_priority_mode', $state['appointment_priority_mode'] ?? AppointmentPriorityMode::Standard->value);

        DateHelper::flushTimezoneCache();

        Notification::make()
            ->title('Hospital settings saved successfully.')
            ->success()
            ->send();
    }
}
