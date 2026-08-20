<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class DmsClassification extends Model
{
    use Cacheable, HasFactory;

    protected $fillable = [
        'name',
        'code',
        'security_level',
        'retention_years',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'retention_years' => 'integer',
        ];
    }

    /**
     * Full classification list, used by the document/folder classification
     * pickers. Small reference table, rarely changes.
     */
    public static function cachedAll(): Collection
    {
        return static::rememberCache(fn () => static::query()->orderBy('name')->get());
    }

    public function folders(): HasMany
    {
        return $this->hasMany(DmsFolder::class, 'classification_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(DmsDocument::class, 'classification_id');
    }
}
