<?php

namespace App\Models;

use App\Enum\PurchaseOrderStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'po_number',
        'vendor_name',
        'status',
        'total_amount',
        'approved_by',
        'approved_at',
        'received_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => PurchaseOrderStatus::class,
            'total_amount' => 'decimal:2',
            'approved_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public static function generatePoNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->format('Y');
            $month = now()->format('m');

            $count = self::where('po_number', 'like', "PO/{$year}/{$month}/%")
                ->lockForUpdate()
                ->count();

            return sprintf('PO/%s/%s/%04d', $year, $month, $count + 1);
        });
    }
}
