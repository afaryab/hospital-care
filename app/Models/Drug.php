<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Drug extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'generic_name',
        'type',
        'drug_category_id',
        'strength',
        'manufacturer',
        'default_dose',
        'default_frequency',
        'default_duration',
        'default_route',
        'usage_instructions',
        'contraindications',
        'side_effects',
        'is_active',
        'old_id',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DrugCategory::class, 'drug_category_id');
    }

    public static function types(): array
    {
        return ['Tablet', 'Capsule', 'Syrup', 'Injection', 'Cream', 'Drops', 'Inhaler', 'Patch', 'Powder', 'Suppository', 'Gel', 'Lotion', 'Other'];
    }

    public static function routes(): array
    {
        return ['Oral', 'IV', 'IM', 'SC', 'Topical', 'Inhalation', 'Sublingual', 'Rectal', 'Nasal', 'Ophthalmic', 'Otic'];
    }

    public static function frequencies(): array
    {
        return ['OD', 'BD', 'TDS', 'QID', 'SOS', 'STAT', 'Nocte', 'Mane', 'Weekly', 'Fortnightly', 'Monthly'];
    }
}
