<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Icd10Code extends Model
{
    use Cacheable, HasFactory;

    protected $fillable = [
        'code',
        'description',
        'category',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Distinct active categories (WHO chapter titles), used to populate the
     * category filter on the ICD-10 picker. Not the full ~11k-code table —
     * that's searched directly (Icd10CodeController), never listed wholesale,
     * so caching it wouldn't serve the actual hot path.
     */
    public static function cachedCategories(): Collection
    {
        return static::rememberCache(fn () => static::query()
            ->where('is_active', true)
            ->whereNotNull('category')
            ->distinct()
            ->orderBy('category')
            ->pluck('category'));
    }
}
