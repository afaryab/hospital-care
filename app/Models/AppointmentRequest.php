<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use App\Enum\AppointmentRequestStatus;
use App\Helpers\PiiHasher;
use Database\Factories\AppointmentRequestFactory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

/**
 * A booking request submitted by a member of the public. It holds only what
 * reception needs to call back and confirm; reception then matches or
 * registers the patient and books the real Appointment.
 */
class AppointmentRequest extends Model
{
    /** @use HasFactory<AppointmentRequestFactory> */
    use HasFactory, LogsActivity, SoftDeletes;

    public const PREFERRED_TIMES = [
        'morning' => 'Morning (9am – 12pm)',
        'afternoon' => 'Afternoon (12pm – 4pm)',
        'evening' => 'Evening (4pm – 8pm)',
    ];

    protected $fillable = [
        'reference',
        'name',
        'contact',
        'gender',
        'age_years',
        'service_id',
        'preferred_date',
        'preferred_time',
        'notes',
        'status',
        'patient_id',
        'appointment_id',
        'handled_by',
        'handled_at',
        'rejection_reason',
        'ip_address',
    ];

    protected $hidden = ['contact_hash', 'ip_address'];

    protected function casts(): array
    {
        return [
            'contact' => SafeEncrypted::class,
            'preferred_date' => 'date',
            'handled_at' => 'datetime',
            'status' => AppointmentRequestStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (AppointmentRequest $request): void {
            $request->reference ??= (string) Str::uuid();
        });

        static::saving(function (AppointmentRequest $request): void {
            if ($request->isDirty('contact')) {
                $request->contact_hash = PiiHasher::contact((string) $request->contact);
            }
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['status', 'patient_id', 'appointment_id', 'handled_by', 'rejection_reason'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function appointment(): BelongsTo
    {
        return $this->belongsTo(Appointment::class);
    }

    public function handler(): BelongsTo
    {
        return $this->belongsTo(User::class, 'handled_by');
    }

    /**
     * Existing patients sharing this request's phone number.
     *
     * @return Collection<int, Patient>
     */
    public function matchingPatients()
    {
        return Patient::query()->where('contact_hash', $this->contact_hash)->latest('id')->limit(5)->get();
    }
}
