<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class DmsDocument extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, LogsActivity, SoftDeletes;

    public const VERSIONS_COLLECTION = 'versions';

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    protected $fillable = [
        'uuid',
        'folder_id',
        'name',
        'classification_id',
        'owner_type',
        'owner_id',
        'status',
        'is_locked',
        'locked_by',
        'locked_at',
        'current_version',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'current_version' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DmsDocument $document): void {
            if (blank($document->uuid)) {
                $document->uuid = (string) Str::uuid();
            }
        });
    }

    /**
     * Versions are stored on the private "local" disk — documents may
     * contain PHI and must never be reachable through the public disk.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::VERSIONS_COLLECTION)
            ->useDisk('local');
    }

    public function folder(): BelongsTo
    {
        return $this->belongsTo(DmsFolder::class, 'folder_id');
    }

    public function classification(): BelongsTo
    {
        return $this->belongsTo(DmsClassification::class, 'classification_id');
    }

    public function owner(): MorphTo
    {
        return $this->morphTo();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(DmsShare::class, 'document_id');
    }

    public function versionMedia(): Collection
    {
        return $this->getMedia(self::VERSIONS_COLLECTION)
            ->sortBy(fn (Media $media) => (int) $media->getCustomProperty('version_number'))
            ->values();
    }

    public function currentVersionMedia(): ?Media
    {
        return $this->getMedia(self::VERSIONS_COLLECTION)
            ->first(fn (Media $media) => (int) $media->getCustomProperty('version_number') === $this->current_version);
    }

    public function latestVersionNumber(): int
    {
        return (int) $this->getMedia(self::VERSIONS_COLLECTION)
            ->max(fn (Media $media) => (int) $media->getCustomProperty('version_number'));
    }
}
