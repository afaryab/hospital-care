<?php

namespace App\Models;

use App\Enum\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class StockMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_item_id',
        'type',
        'quantity',
        'unit_cost',
        'reference_type',
        'reference_id',
        'department_id',
        'batch_number',
        'expiry_date',
        'moved_by',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'type' => StockMovementType::class,
            'quantity' => 'decimal:2',
            'unit_cost' => 'decimal:2',
            'expiry_date' => 'date',
        ];
    }

    public function stockItem(): BelongsTo
    {
        return $this->belongsTo(StockItem::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ServiceDepartment::class, 'department_id');
    }

    public function movedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moved_by');
    }

    public function reference(): MorphTo
    {
        return $this->morphTo();
    }
}
