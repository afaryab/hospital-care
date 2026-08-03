<?php

namespace App\Models;

use App\Enum\AppointmentPriorityMode;
use App\Enum\AppointmentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class Appointment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'appointment_number',
        'patient_id',
        'service_id',
        'doctor_id',
        'scheduled_at',
        'priority_mode',
        'status',
        'service_order_id',
        'receaveable_id',
        'checked_in_at',
        'cancelled_at',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'priority_mode' => AppointmentPriorityMode::class,
            'status' => AppointmentStatus::class,
            'scheduled_at' => 'datetime',
            'checked_in_at' => 'datetime',
            'cancelled_at' => 'datetime',
        ];
    }

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function serviceOrder(): BelongsTo
    {
        return $this->belongsTo(ServiceOrder::class);
    }

    public function receaveable(): BelongsTo
    {
        return $this->belongsTo(Receaveable::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public static function generateAppointmentNumber(): string
    {
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');
            $prefix = "APT/{$year}/{$month}/";

            $existingNumbers = self::withTrashed()
                ->where('appointment_number', 'like', "{$prefix}%")
                ->lockForUpdate()
                ->pluck('appointment_number');

            $maxSequence = 0;
            foreach ($existingNumbers as $appointmentNumber) {
                $parts = explode('/', (string) $appointmentNumber);
                $sequence = (int) ($parts[3] ?? 0);
                if ($sequence > $maxSequence) {
                    $maxSequence = $sequence;
                }
            }

            $nextSequence = $maxSequence + 1;
            $count = str_pad((string) $nextSequence, 4, '0', STR_PAD_LEFT);

            return "{$prefix}{$count}";
        });
    }
}
