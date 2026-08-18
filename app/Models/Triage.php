<?php

namespace App\Models;

use App\Concerns\Cacheable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;

class Triage extends Model
{
    use Cacheable, HasFactory;

    protected $fillable = [
        'name',
        'color',
        'priority',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'priority' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function treatmentRecords(): HasMany
    {
        return $this->hasMany(TreatmentRecord::class);
    }

    public static function colorOptions(): array
    {
        return [
            'red' => '🔴 Red',
            'yellow' => '🟡 Yellow',
            'blue' => '🔵 Blue',
            'sky' => '🩵 Sky Blue',
            'green' => '🟢 Green',
            'black' => '⚫ Black',
        ];
    }

    /**
     * Active triage levels, used across EMG queue and treatment forms.
     * Small and rarely changes.
     */
    public static function cachedActive(): Collection
    {
        return static::rememberCache(fn () => static::query()->where('is_active', true)->orderBy('priority')->get());
    }
}
