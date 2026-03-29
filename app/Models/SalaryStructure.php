<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'basic_salary',
        'housing_allowance',
        'medical_allowance',
        'transport_allowance',
        'other_allowances',
        'effective_from',
        'effective_to',
    ];

    protected function casts(): array
    {
        return [
            'other_allowances' => 'json',
            'basic_salary' => 'decimal:2',
            'housing_allowance' => 'decimal:2',
            'medical_allowance' => 'decimal:2',
            'transport_allowance' => 'decimal:2',
            'effective_from' => 'date',
            'effective_to' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payslipEntries(): HasMany
    {
        return $this->hasMany(PayslipEntry::class);
    }
}
