<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class Patient extends Model
{
    use HasFactory, LogsActivity, SoftDeletes;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'id',
        'ps_number',
        'name',
        'gender',
        'age_group',
        'age_days',
        'age_dob',
        'address',
        'guardian',
        'relation',
        'contact',
        'contact_hash',
        'cnic',
        'cnic_hash',
        'created_at',
        'updated_at',
    ];

    protected function casts(): array
    {
        return [
            'cnic' => SafeEncrypted::class,
            'contact' => SafeEncrypted::class,
            'address' => SafeEncrypted::class,
        ];
    }

    protected $appends = [
        'age',
        'year',
        'month',
        'number',
        'outstandings',
    ];

    protected static function booted(): void
    {
        static::updating(function (Patient $patient): void {
            PatientVersion::query()->create([
                'patient_id' => $patient->id,
                'snapshot' => $patient->getOriginal(),
                'change_reason' => 'record_update',
                'changed_by' => auth()->id(),
                'changed_at' => now(),
            ]);
        });

        static::deleting(function (Patient $patient): void {
            if ($patient->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for patient records.');
            }
        });
    }

    public function getAgeAttribute()
    {
        // Calculate age based on age_dob and created_at if age_dob is available
        if ($this->age_dob !== null) {
            $birthDate = Carbon::parse($this->age_dob);
            $formToday = Carbon::now();
            $ageInYears = $birthDate->diffInYears($formToday);

            return (int) $ageInYears;
        }

        // User age days and created at to calculate age in years with respect to today
        if ($this->age_days !== null) {
            $createdAt = $this->created_at ?? Carbon::now();
            $birthDate = $createdAt->copy()->subDays($this->age_days);
            $formToday = Carbon::now();
            $ageInYears = $birthDate->diffInYears($formToday);

            return (int) $ageInYears;
        }
    }

    public function getYearAttribute()
    {
        return $this->ps_number_parts['year'] ?? null;
    }

    public function getMonthAttribute()
    {
        return $this->ps_number_parts['month'] ?? null;
    }

    public function getNumberAttribute()
    {
        return $this->ps_number_parts['number'] ?? null;
    }

    public function getPsNumberPartsAttribute()
    {
        if (empty($this->ps_number)) {
            return null;
        }

        $parts = explode('/', $this->ps_number);

        return [
            'year' => $parts[1] ?? null,
            'month' => $parts[2] ?? null,
            'number' => $parts[3] ?? null,
        ];
    }

    public static function generateCounterNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $prefix = "PS/{$year}/{$month}/";

            $existingNumbers = self::where('ps_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('ps_number');

            $maxSequence = 0;
            foreach ($existingNumbers as $psNumber) {
                $parts = explode('/', (string) $psNumber);
                $sequence = (int) ($parts[3] ?? 0);
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
            }

            $nextSequence = $maxSequence + 1;
            $count = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            return "{$prefix}{$count}";
        });
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'patient_id', 'id');
    }

    public function treatments()
    {
        return $this->hasMany(ServiceOrder::class, 'patient_id', 'id');
    }

    public function receaveables()
    {
        return $this->hasMany(Receaveable::class, 'patient_id', 'id')->where('status', 'unpaid')->whereNull('panel_id');
    }

    public function versions(): HasMany
    {
        return $this->hasMany(PatientVersion::class)->latest('changed_at');
    }

    public function getOutstandingsAttribute()
    {
        // Sum all unpaid receaveables for this patient
        $total = $this->receaveables()->sum('amount');

        // Only return a value if there is something outstanding
        return $total > 0 ? $total : null;
    }
}
