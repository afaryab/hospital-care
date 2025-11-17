<?php

namespace App\Filament\Admin\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Select;
use App\Models\Administrator;
use App\Models\Accountant;
use App\Models\Receptionist;
use App\Models\OpdDoctor;
use App\Models\IndDoctor;
use App\Models\EmergencyDoctor;
use App\Models\Dentist;
use App\Models\UltrasoundDoctor;
use App\Models\XrayTechnician;
use App\Models\NursingStaff;
use App\Models\PatientManager;
use App\Models\Patient;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('User Information')
                    ->schema([
                        ImageEntry::make('profile_img_path')
                            ->label('Profile Picture')
                            ->circular()
                            ->size(120)
                            ->defaultImageUrl(url('/images/default-avatar.png'))
                            ->columnSpanFull(),
                        
                        TextEntry::make('name')
                            ->label('Full Name')
                            ->weight(FontWeight::Bold)
                            ->size('lg')
                            ->color(Color::Blue)
                            ->icon('heroicon-o-user'),
                        
                        TextEntry::make('email')
                            ->label('Email Address')
                            ->icon('heroicon-o-envelope')
                            ->copyable()
                            ->copyMessage('Email copied!')
                            ->copyMessageDuration(1500),
                        
                        IconEntry::make('email_verified_at')
                            ->label('Email Verified')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-badge')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor(Color::Green)
                            ->falseColor(Color::Red),
                        
                        IconEntry::make('is_active')
                            ->label('Active Status')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor(Color::Green)
                            ->falseColor(Color::Red),
                    ])
                    ->icon('heroicon-o-identification')
                    ->collapsible()
                    ->columns(2),
                
                Section::make('Security Information')
                    ->schema([
                        TextEntry::make('last_login')
                            ->label('Last Login')
                            ->dateTime('M j, Y \a\t g:i A')
                            ->icon('heroicon-o-calendar')
                            ->placeholder('Never logged in')
                            ->color(Color::Green),
                        
                        TextEntry::make('last_activity')
                            ->label('Last Activity')
                            ->dateTime('M j, Y \a\t g:i A')
                            ->icon('heroicon-o-clock')
                            ->placeholder('No activity recorded'),
                        
                        TextEntry::make('ip_address')
                            ->label('Last IP Address')
                            ->icon('heroicon-o-globe-alt')
                            ->placeholder('Unknown')
                            ->copyable(),
                        
                        TextEntry::make('login_attempts')
                            ->label('Login Attempts')
                            ->icon('heroicon-o-key')
                            ->numeric()
                            ->color(fn ($state) => $state > 3 ? Color::Red : Color::Gray),
                        
                        TextEntry::make('password_expired_at')
                            ->label('Password Expires')
                            ->dateTime('M j, Y')
                            ->icon('heroicon-o-shield-exclamation')
                            ->placeholder('Never expires'),
                        
                        TextEntry::make('two_factor_confirmed_at')
                            ->label('2FA Enabled')
                            ->dateTime('M j, Y \a\t g:i A')
                            ->icon('heroicon-o-device-phone-mobile')
                            ->placeholder('Not enabled')
                            ->color(Color::Blue),
                    ])
                    ->icon('heroicon-o-shield-check')
                    ->collapsible()
                    ->columns(2),
                
                Section::make('User Profiles & Roles')
                    ->description('📝 To manage user profiles and roles, click the "Edit" button above.')
                    ->schema([
                        TextEntry::make('adminProfiles')
                            ->label('Administrator')
                            ->formatStateUsing(function ($state) {
                                if (!$state || $state->count() === 0) {
                                    return '❌ Not assigned';
                                }
                                return $state->map(function ($profile) {
                                    return '👑 ' . ucfirst($profile->authority);
                                })->implode(', ');
                            })
                            ->icon('heroicon-o-shield-check')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Purple)
                            ->badge(),
                        
                        TextEntry::make('accountantProfiles')
                            ->label('Accountant')
                            ->formatStateUsing(function ($state) {
                                if (!$state || $state->count() === 0) {
                                    return '❌ Not assigned';
                                }
                                return $state->map(function ($profile) {
                                    return '💼 ' . ucfirst($profile->authority);
                                })->implode(', ');
                            })
                            ->icon('heroicon-o-calculator')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Green)
                            ->badge(),
                        
                        TextEntry::make('receptionistProfiles')
                            ->label('Receptionist')
                            ->formatStateUsing(function ($state) {
                                if (!$state || $state->count() === 0) {
                                    return '❌ Not assigned';
                                }
                                return $state->map(function ($profile) {
                                    return '📞 ' . ucfirst($profile->authority);
                                })->implode(', ');
                            })
                            ->icon('heroicon-o-phone')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Blue)
                            ->badge(),
                        
                        TextEntry::make('opdDoctorProfiles')
                            ->label('OPD Doctor')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-user-circle')
                            ->color(function ($state) {
                                return !$state || $state->count() === 0 ? Color::Gray : Color::Green;
                            })
                            ->badge(),
                        
                        TextEntry::make('indDoctorProfiles')
                            ->label('Inpatient Doctor')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-building-office-2')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Teal)
                            ->badge(),
                        
                        TextEntry::make('emergencyDoctorProfiles')
                            ->label('Emergency Doctor')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-bolt')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Red)
                            ->badge(),
                        
                        TextEntry::make('dentistProfiles')
                            ->label('Dentist')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-face-smile')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Orange)
                            ->badge(),
                        
                        TextEntry::make('ultrasoundDoctorProfiles')
                            ->label('Ultrasound Doctor')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-radio')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Cyan)
                            ->badge(),
                        
                        TextEntry::make('xrayTechnicianProfiles')
                            ->label('X-Ray Technician')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-camera')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Violet)
                            ->badge(),
                        
                        TextEntry::make('nursingStaffProfiles')
                            ->label('Nursing Staff')
                            ->formatStateUsing(function ($state) {
                                return !$state || $state->count() === 0 ? '❌ Not assigned' : '✅ Assigned (' . $state->count() . ')';
                            })
                            ->icon('heroicon-o-heart')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Pink)
                            ->badge(),
                        
                        TextEntry::make('patientManagerProfiles')
                            ->label('Patient Manager')
                            ->formatStateUsing(function ($state) {
                                if (!$state || $state->count() === 0) {
                                    return 'Not assigned to any patient';
                                }
                                $profiles = $state->map(function ($profile) {
                                    if ($profile->patient_id) {
                                        $patient = Patient::find($profile->patient_id);
                                        return $patient ? "👥 Managing: {$patient->name} (ID: {$patient->id})" : "❌ Patient not found (ID: {$profile->patient_id})";
                                    }
                                    return "⚠️ No patient assigned";
                                });
                                return $profiles->implode('<br>');
                            })
                            ->html()
                            ->icon('heroicon-o-users')
                            ->color(fn ($state) => !$state || $state->count() === 0 ? Color::Gray : Color::Indigo)
                            ->badge(),
                    ])
                    ->icon('heroicon-o-user-group')
                    ->collapsible()
                    ->columns(3),
                
                Section::make('Account Status')
                    ->schema([
                        TextEntry::make('banned_message')
                            ->label('Ban Reason')
                            ->placeholder('Not banned')
                            ->icon('heroicon-o-no-symbol')
                            ->color(Color::Red)
                            ->weight(FontWeight::Medium)
                            ->visible(fn ($record) => !empty($record->banned_message)),
                        
                        TextEntry::make('email_verified_at')
                            ->label('Email Verified On')
                            ->dateTime('F j, Y \a\t g:i A')
                            ->placeholder('Email not verified')
                            ->icon('heroicon-o-envelope-open')
                            ->color(Color::Green),
                        
                        TextEntry::make('created_at')
                            ->label('Member Since')
                            ->dateTime('F j, Y')
                            ->icon('heroicon-o-calendar-days')
                            ->color(Color::Blue)
                            ->weight(FontWeight::Medium),
                        
                        TextEntry::make('updated_at')
                            ->label('Last Updated')
                            ->dateTime('M j, Y \a\t g:i A')
                            ->icon('heroicon-o-pencil')
                            ->color(Color::Gray),
                    ])
                    ->icon('heroicon-o-information-circle')
                    ->columns(2),
                
                Section::make('Two-Factor Authentication')
                    ->schema([
                        TextEntry::make('two_factor_secret')
                            ->label('2FA Secret')
                            ->placeholder('Not configured')
                            ->formatStateUsing(fn ($state) => $state ? 'Configured' : 'Not configured')
                            ->icon('heroicon-o-key')
                            ->color(fn ($state) => $state ? Color::Green : Color::Gray),
                        
                        TextEntry::make('two_factor_recovery_codes')
                            ->label('Recovery Codes')
                            ->placeholder('Not generated')
                            ->formatStateUsing(fn ($state) => $state ? 'Generated' : 'Not generated')
                            ->icon('heroicon-o-document-text')
                            ->color(fn ($state) => $state ? Color::Green : Color::Gray),
                        
                        TextEntry::make('two_factor_confirmed_at')
                            ->label('2FA Confirmed')
                            ->dateTime('F j, Y \a\t g:i A')
                            ->placeholder('Not confirmed')
                            ->icon('heroicon-o-shield-check')
                            ->color(fn ($state) => $state ? Color::Green : Color::Gray),
                    ])
                    ->icon('heroicon-o-device-phone-mobile')
                    ->visible(fn ($record) => $record->two_factor_secret || $record->two_factor_recovery_codes || $record->two_factor_confirmed_at)
                    ->columns(3),
            ]);
    }
}