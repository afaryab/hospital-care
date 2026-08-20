<?php

namespace App\Models;

use App\Casts\SafeEncrypted;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReferralCertificate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'service_order_id',
        'referral_number',
        'receiving_facility_name',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'notes' => SafeEncrypted::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ReferralCertificate $certificate): void {
            if (empty($certificate->referral_number)) {
                $certificate->referral_number = static::generateReferralNumber();
            }
        });

        static::deleting(function (ReferralCertificate $certificate): void {
            if ($certificate->isForceDeleting()) {
                throw new \RuntimeException('Hard delete is not allowed for referral certificates.');
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

    /**
     * Best-effort HTML sanitization for doctor-authored referral notes.
     * There is no HTML purifier package in this project (adding one is a
     * dependency decision beyond this feature's scope), so this only
     * allowlists CKEditor's basic formatting tags and strips event-handler
     * attributes / javascript: URIs on whatever tags remain.
     */
    public static function sanitizeNotes(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return null;
        }

        $allowedTags = '<p><br><strong><b><em><i><u><ul><ol><li><span><h1><h2><h3><h4><blockquote><a>';
        $sanitized = strip_tags($html, $allowedTags);
        $sanitized = preg_replace('/\son\w+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $sanitized);
        $sanitized = preg_replace('/(href|src)\s*=\s*(["\'])\s*javascript:.*?\2/i', '$1=""', $sanitized);

        return $sanitized;
    }

    public static function generateReferralNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $prefix = "RF/{$year}/{$month}/";

            $existingNumbers = self::withTrashed()
                ->where('referral_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('referral_number');

            $maxSequence = 0;
            foreach ($existingNumbers as $referralNumber) {
                $parts = explode('/', (string) $referralNumber);
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
