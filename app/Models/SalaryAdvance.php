<?php

namespace App\Models;

use App\Enum\SalaryAdvanceStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'amount',
        'granted_by',
        'granted_at',
        'deduction_per_month',
        'remaining_balance',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SalaryAdvanceStatus::class,
            'amount' => 'decimal:2',
            'deduction_per_month' => 'decimal:2',
            'remaining_balance' => 'decimal:2',
            'granted_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function grantedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'granted_by');
    }
}
