<?php

namespace App\Models;

use App\Enum\TaskPriority;
use App\Enum\TaskStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'task_number',
        'title',
        'description',
        'priority',
        'status',
        'assigned_to',
        'assigned_by',
        'department_id',
        'due_date',
        'completed_at',
        'completion_notes',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'due_date' => 'datetime',
            'completed_at' => 'datetime',
        ];
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(ServiceDepartment::class, 'department_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class);
    }

    public static function generateTaskNumber(): string
    {
        return DB::transaction(function () {
            $year = now()->format('Y');
            $month = now()->format('m');

            $count = self::where('task_number', 'like', "TSK/{$year}/{$month}/%")
                ->lockForUpdate()
                ->count();

            return sprintf('TSK/%s/%s/%04d', $year, $month, $count + 1);
        });
    }
}
