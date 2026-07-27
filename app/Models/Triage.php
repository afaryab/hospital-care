<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Triage extends Model
{
    use HasFactory;

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
}
