<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Collection;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Permission\Traits\HasRoles;

/**
 * Implementing MustVerifyEmail is not cosmetic: Laravel's `verified`
 * middleware — already applied throughout routes/web.php and
 * routes/settings.php — checks `$user instanceof MustVerifyEmail` and is a
 * silent no-op for any model that doesn't implement it, regardless of
 * `email_verified_at`. Fortify's Features::emailVerification() being
 * enabled in config/fortify.php only wires up the verification *flow*
 * (sending the email, the /email/verify routes) — it does not, by itself,
 * make anything actually require verification.
 */
class User extends Authenticatable implements FilamentUser, MustVerifyEmailContract
{
    /** @use HasFactory<UserFactory> */
    use Cacheable, HasFactory, HasRoles, LogsActivity, MustVerifyEmail, Notifiable, TwoFactorAuthenticatable;

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
            'lcd_opd' => $this->lcdOpdProfiles,
            'lcd_ind' => $this->lcdIndProfiles,
            'lcd_emergency' => $this->lcdEmergencyProfiles,
            'lcd_dental' => $this->lcdDentalProfiles,
            'lcd_laboratory' => $this->lcdLaboratoryProfiles,
            'lcd_ultrasound' => $this->lcdUltrasoundProfiles,
            'lcd_xray' => $this->lcdXrayProfiles,
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

    public function lcdOpdProfiles()
    {

        return $this->hasMany(LcdOpdOperator::class);
    }

    public function lcdIndProfiles()
    {

        return $this->hasMany(LcdIndOperator::class);
    }

    public function lcdEmergencyProfiles()
    {

        return $this->hasMany(LcdEmergencyOperator::class);
    }

    public function lcdDentalProfiles()
    {

        return $this->hasMany(LcdDentalOperator::class);
    }

    public function lcdLaboratoryProfiles()
    {

        return $this->hasMany(LcdLaboratoryOperator::class);
    }

    public function lcdUltrasoundProfiles()
    {

        return $this->hasMany(LcdUltrasoundOperator::class);
    }

    public function lcdXrayProfiles()
    {

        return $this->hasMany(LcdXrayOperator::class);
    }

    // ─── Role helpers ────────────────────────────────────────────────────────

    /**
     * Memoized per-instance: every Policy's before() hook calls this first,
     * so on a Filament table with row-level actions it previously ran once
     * per row per action visibility check — a single admin-panel page load
     * could fire dozens of identical `adminProfiles` existence queries.
     */
    protected ?bool $isAdminMemo = null;

    public function isAdmin(): bool
    {
        return $this->isAdminMemo ??= $this->adminProfiles()->exists();
    }

    public function isAccountant(): bool
    {
        return $this->accountantProfiles()->exists();
    }

    public function isReceptionist(): bool
    {
        return $this->receptionistProfiles()->exists();
    }

    /**
     * Every user holding at least one doctor/provider profile (OPD, IND,
     * EMG, dentist, ultrasound, X-ray) — the "Doctor / Provider" dropdown
     * repeated across transaction, service order, and treatment forms.
     * Cached under its own key rather than the model-wide one: this is a
     * filtered subset of users, not "all users".
     */
    public static function cacheKey(): string
    {
        return 'model-cache:users:doctors';
    }

    public static function cachedDoctors(): Collection
    {
        return static::rememberCache(fn () => static::query()
            ->where(fn ($q) => $q
                ->whereHas('opdDoctorProfiles')
                ->orWhereHas('indDoctorProfiles')
                ->orWhereHas('emergencyDoctorProfiles')
                ->orWhereHas('dentistProfiles')
                ->orWhereHas('ultrasoundDoctorProfiles')
                ->orWhereHas('xrayTechnicianProfiles'))
            ->orderBy('name')
            ->get());
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

    /**
     * Maps a ServiceDepartment slug to the relation name for the LCD
     * (department queue display) profile scoped to that department.
     *
     * @var array<string, string>
     */
    public const LCD_DEPARTMENT_RELATIONS = [
        'OPD' => 'lcdOpdProfiles',
        'IND' => 'lcdIndProfiles',
        'EMG' => 'lcdEmergencyProfiles',
        'DNT' => 'lcdDentalProfiles',
        'PTH' => 'lcdLaboratoryProfiles',
        'ULT' => 'lcdUltrasoundProfiles',
        'XRAY' => 'lcdXrayProfiles',
    ];

    public function isLcdOperator(): bool
    {
        foreach (self::LCD_DEPARTMENT_RELATIONS as $relation) {
            if ($this->{$relation}()->exists()) {
                return true;
            }
        }

        return false;
    }

    public function hasLcdAccessTo(string $departmentSlug): bool
    {
        $relation = self::LCD_DEPARTMENT_RELATIONS[$departmentSlug] ?? null;

        return $relation !== null && $this->{$relation}()->exists();
    }

    /**
     * Department slugs (ServiceDepartment.slug values) this user operates
     * an LCD queue display for.
     *
     * @return array<int, string>
     */
    public function lcdDepartmentSlugs(): array
    {
        return array_values(array_filter(
            array_keys(self::LCD_DEPARTMENT_RELATIONS),
            fn (string $slug): bool => $this->hasLcdAccessTo($slug)
        ));
    }

    public function hasAnyProfile(): bool
    {
        return $this->isAdmin()
            || $this->isAccountant()
            || $this->isReceptionist()
            || $this->isAnyDoctor()
            || $this->isPatientManager()
            || $this->nursingStaffProfiles()->exists()
            || $this->isLcdOperator();
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
