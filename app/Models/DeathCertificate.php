<?php

namespace App\Models;

use App\Enum\DeathCertificateManner;
use App\Models\Concerns\HasVerificationToken;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeathCertificate extends Model
{
    use HasFactory, HasVerificationToken, SoftDeletes;

    protected $fillable = [
        'service_order_id',
        'certificate_number',
        'verification_token',
        'date_of_death',
        'time_of_death',
        'place_of_death',
        'manner_of_death',
        'antecedent_cause',
        'informant_name',
        'informant_relation',
        'informant_cnic',
        'remarks',
        'is_locked',
        'locked_at',
        'locked_by',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'date_of_death' => 'date',
            'manner_of_death' => DeathCertificateManner::class,
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DeathCertificate $certificate): void {
            if (empty($certificate->certificate_number)) {
                $certificate->certificate_number = static::generateCertificateNumber();
            }
        });

        static::updating(function (DeathCertificate $certificate): void {
            $dirtyAttributes = array_diff(array_keys($certificate->getDirty()), ['updated_at']);

            if ($certificate->getOriginal('is_locked') && count($dirtyAttributes) > 0) {
                throw ValidationException::withMessages([
                    'death_certificate' => 'Locked death certificates cannot be modified.',
                ]);
            }
        });

        static::deleting(function (DeathCertificate $certificate): void {
            if ($certificate->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for death certificates.');
            }
        });
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public static function generateCertificateNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $prefix = "DC/{$year}/{$month}/";

            $existingNumbers = self::withTrashed()
                ->where('certificate_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('certificate_number');

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
