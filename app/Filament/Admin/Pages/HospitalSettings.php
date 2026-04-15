<?php

namespace App\Filament\Admin\Pages;

use App\Models\HospitalSetting;
use Filament\Forms\Components\FileUpload;
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
            'abacus_auto_map_accounts' => (bool) HospitalSetting::get('abacus_auto_map_accounts', false),
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
                Toggle::make('abacus_auto_map_accounts')
                    ->label('Auto Map Accounts (Abacus)')
                    ->helperText('When enabled, receiving a closing statement will automatically create Abacus accounting entries.'),
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
        HospitalSetting::set('abacus_auto_map_accounts', $state['abacus_auto_map_accounts'] ?? false);

        Notification::make()
            ->title('Hospital settings saved successfully.')
            ->success()
            ->send();
    }
}
