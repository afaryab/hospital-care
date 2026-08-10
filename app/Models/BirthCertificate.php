<?php

namespace App\Models;

use App\Models\Concerns\HasVerificationToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BirthCertificate extends Model
{
    use HasFactory, HasVerificationToken, SoftDeletes;

    protected $fillable = [
        'service_order_id',
        'birth_certificate_number',
        'verification_token',
        'child_name',
        'date_of_birth',
        'time_of_birth',
        'gender',
        'place_of_birth',
        'weight_at_birth',
        'mother_name',
        'mother_cnic',
        'father_name',
        'father_cnic',
        'attending_doctor_id',
        'remarks',
        'is_locked',
        'locked_at',
        'locked_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
            'weight_at_birth' => 'decimal:2',
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (BirthCertificate $certificate): void {
            if (empty($certificate->birth_certificate_number)) {
                $certificate->birth_certificate_number = static::generateCertificateNumber();
            }
        });

        static::updating(function (BirthCertificate $certificate): void {
            $dirtyAttributes = array_diff(array_keys($certificate->getDirty()), ['updated_at']);

            if ($certificate->getOriginal('is_locked') && count($dirtyAttributes) > 0) {
                throw ValidationException::withMessages([
                    'birth_certificate' => 'Locked birth certificates cannot be modified.',
                ]);
            }
        });

        static::deleting(function (BirthCertificate $certificate): void {
            if ($certificate->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for birth certificates.');
            }
        });
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function attendingDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'attending_doctor_id');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateCertificateNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $prefix = "BC/{$year}/{$month}/";

            $existingNumbers = self::withTrashed()
                ->where('birth_certificate_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('birth_certificate_number');

            $maxSequence = 0;
            foreach ($existingNumbers as $certificateNumber) {
                $parts = explode('/', (string) $certificateNumber);
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
}
