<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements FilamentUser
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, HasRoles, LogsActivity, Notifiable, TwoFactorAuthenticatable;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public const SYSTEM_SEEDER_EMAIL = 'system-seeder@hospital-care.local';

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'id',
        'name',
        'username',
        'email',
        'mobile',
        'password',
        'password_expired_at',
        'last_login',
        'last_activity',
        'last_login_attempt',
        'ip_address',
        'login_attempts',
        'profile_img_path',
        'profile_img_id',
        'is_active',
        'banned_message',
        'timezone',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    /**
     * Append the profiles attribute to the model's array form.
     *
     * @var array
     */
    protected $appends = ['profiles'];

    public function scopeNonSystem($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('email')
                ->orWhere('email', '!=', self::SYSTEM_SEEDER_EMAIL);
        });
    }

    public function getProfilesAttribute()
    {

        return [
            'admin' => $this->adminProfiles,
            'accountant' => $this->accountantProfiles,
            'receptionist' => $this->receptionistProfiles,
            'opd_doctor' => $this->opdDoctorProfiles,
            'ind_doctor' => $this->indDoctorProfiles,
            'emergency_doctor' => $this->emergencyDoctorProfiles,
            'dentist' => $this->dentistProfiles,
            'ultrasound_doctor' => $this->ultrasoundDoctorProfiles,
            'xray_technician' => $this->xrayTechnicianProfiles,
            'nursing_staff' => $this->nursingStaffProfiles,
            'patient_manager' => $this->patientManagerProfiles,
        ];
    }

    public function adminProfiles()
    {

        return $this->hasMany(Administrator::class);
    }

    public function accountantProfiles()
    {

        return $this->hasMany(Accountant::class);
    }

    public function receptionistProfiles()
    {

        return $this->hasMany(Receptionist::class);
    }

    public function opdDoctorProfiles()
    {

        return $this->hasMany(OpdDoctor::class);
    }

    public function indDoctorProfiles()
    {

        return $this->hasMany(IndDoctor::class);
    }

    public function emergencyDoctorProfiles()
    {

        return $this->hasMany(EmergencyDoctor::class);
    }

    public function dentistProfiles()
    {

        return $this->hasMany(Dentist::class);
    }

    public function ultrasoundDoctorProfiles()
    {

        return $this->hasMany(UltrasoundDoctor::class);
    }

    public function xrayTechnicianProfiles()
    {

        return $this->hasMany(XrayTechnician::class);
    }

    public function nursingStaffProfiles()
    {

        return $this->hasMany(NursingStaff::class);
    }

    public function patientManagerProfiles()
    {

        return $this->hasMany(PatientManager::class);
    }

    // ─── Role helpers ────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->adminProfiles()->exists();
    }

    public function isAccountant(): bool
    {
        return $this->accountantProfiles()->exists();
    }

    public function isReceptionist(): bool
    {
        return $this->receptionistProfiles()->exists();
    }

    public function isAnyDoctor(): bool
    {
        return $this->opdDoctorProfiles()->exists()
            || $this->indDoctorProfiles()->exists()
            || $this->emergencyDoctorProfiles()->exists()
            || $this->dentistProfiles()->exists()
            || $this->ultrasoundDoctorProfiles()->exists()
            || $this->xrayTechnicianProfiles()->exists();
    }

    /**
     * PMDC (Pakistan Medical & Dental Council) registration number, from
     * whichever doctor/dentist profile this user has one on. Only doctor
     * profiles carry this field — nurses, technicians, and other staff don't.
     */
    public function getPmdcNumberAttribute(): ?string
    {
        return $this->opdDoctorProfiles->first()?->pmdc_number
            ?? $this->indDoctorProfiles->first()?->pmdc_number
            ?? $this->emergencyDoctorProfiles->first()?->pmdc_number
            ?? $this->dentistProfiles->first()?->pmdc_number
            ?? $this->ultrasoundDoctorProfiles->first()?->pmdc_number;
    }

    public function isPatientManager(): bool
    {
        return $this->patientManagerProfiles()->exists();
    }

    public function hasAnyProfile(): bool
    {
        return $this->isAdmin()
            || $this->isAccountant()
            || $this->isReceptionist()
            || $this->isAnyDoctor()
            || $this->isPatientManager()
            || $this->nursingStaffProfiles()->exists();
    }

    /**
     * Determine if the user can access the given Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        // Check panel access based on panel ID and user profiles
        switch ($panel->getId()) {
            case 'admin':
                return $this->adminProfiles()->exists();
            case 'accounts':
                return $this->accountantProfiles()->exists();
            default:
                return false; // Deny access to unknown panels
        }
    }
}
