<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;

class DmsFolder extends Model
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
        'uuid',
        'name',
        'parent_id',
        'path',
        'classification_id',
        'owner_type',
        'owner_id',
        'is_system',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'is_system' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (DmsFolder $folder): void {
            if (blank($folder->uuid)) {
                $folder->uuid = (string) Str::uuid();
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DmsDocument::class, 'folder_id');
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

    public function shares(): HasMany
    {
        return $this->hasMany(DmsShare::class, 'folder_id');
    }

    /**
     * All folders nested anywhere below this one, found via the materialized
     * path prefix rather than a recursive query.
     */
    public function descendantsQuery()
    {
        return self::query()->where('path', 'like', $this->path.$this->id.'/%');
    }

    /**
     * True if $other is this folder itself or one of its ancestors — used to
     * block moving/copying a folder into its own subtree.
     */
    public function isDescendantOf(self $other): bool
    {
        return $this->id === $other->id
            || str_starts_with($this->path, $other->path.$other->id.'/');
    }

    public function fullPathLabel(): string
    {
        $names = [];
        $folder = $this;

        while ($folder !== null) {
            array_unshift($names, $folder->name);
            $folder = $folder->parent;
        }

        return '/'.implode('/', $names);
    }
}
