<?php

namespace App\Models;

use App\Enum\SlipPhotoSource;
use App\Enum\SlipPhotoSubject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * Webcam photo of the patient (or their guardian) taken at the counter for a
 * single slip. It is captured before the slip exists (pending, no
 * transaction_id) and attached to the next income slip generated for that
 * patient at the same counter. A photo snapped before any patient is chosen
 * has no patient_id until claimForPatient() hands it to the next patient
 * selected. Kept apart from the patient's profile photo.
 */
class SlipPhoto extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    public const PHOTO_COLLECTION = 'slip-photo';

    public const CLAIM_WINDOW_MINUTES = 30;

    protected $fillable = [
        'patient_id',
        'closing_id',
        'transaction_id',
        'subject',
        'source',
        'captured_by',
        'captured_at',
    ];

    protected function casts(): array
    {
        return [
            'subject' => SlipPhotoSubject::class,
            'source' => SlipPhotoSource::class,
            'captured_at' => 'datetime',
        ];
    }

    /**
     * Slip photos are PHI, so they live on the private disk.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::PHOTO_COLLECTION)
            ->useDisk('local')
            ->singleFile()
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    public function photo(): ?Media
    {
        return $this->getFirstMedia(self::PHOTO_COLLECTION);
    }

    /**
     * Photos captured at a counter that haven't been attached to a slip yet.
     *
     * @param  Builder<SlipPhoto>  $query
     */
    public function scopePending(Builder $query, int $closingId, int $patientId): void
    {
        $query->where('closing_id', $closingId)
            ->where('patient_id', $patientId)
            ->whereNull('transaction_id');
    }

    /**
     * Photos snapped at a counter before a patient was chosen.
     *
     * @param  Builder<SlipPhoto>  $query
     */
    public function scopeUnassigned(Builder $query, int $closingId): void
    {
        $query->where('closing_id', $closingId)
            ->whereNull('patient_id')
            ->whereNull('transaction_id');
    }

    /**
     * Hand the counter's most recent unassigned photo to the patient the
     * receptionist just selected or created, unless that patient already has
     * one pending. Only photos younger than CLAIM_WINDOW_MINUTES qualify, so a
     * forgotten snap can't end up on an unrelated patient's slip.
     */
    public static function claimForPatient(int $closingId, Patient $patient, ?User $user): ?self
    {
        if (self::query()->pending($closingId, $patient->id)->exists()) {
            return null;
        }

        $slipPhoto = self::query()
            ->unassigned($closingId)
            ->where('captured_at', '>=', now()->subMinutes(self::CLAIM_WINDOW_MINUTES))
            ->latest('id')
            ->first();

        if ($slipPhoto === null) {
            return null;
        }

        $slipPhoto->update(['patient_id' => $patient->id]);

        activity()
            ->causedBy($user)
            ->performedOn($patient)
            ->event('slip_photo_assigned')
            ->withProperties(['slip_photo_id' => $slipPhoto->id, 'closing_id' => $closingId])
            ->log('Slip photo assigned to patient');

        return $slipPhoto;
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function closing(): BelongsTo
    {
        return $this->belongsTo(Closing::class);
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function capturedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'captured_by');
    }

    /**
     * @return array{id: int, subject: string, subject_label: string, source: string, captured_by: ?string, captured_at: ?string, url: string}
     */
    public function toSummary(): array
    {
        return [
            'id' => $this->id,
            'subject' => $this->subject->value,
            'subject_label' => $this->subject->label(),
            'source' => $this->source->value,
            'captured_by' => $this->capturedBy?->name,
            'captured_at' => $this->captured_at?->toIso8601String(),
            'url' => route('slip-photo-show', $this),
        ];
    }
}
