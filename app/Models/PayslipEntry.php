<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PayslipEntry extends Model
{
    use HasFactory;

    protected $fillable = [
        'payroll_period_id',
        'user_id',
        'salary_structure_id',
        'gross_salary',
        'deductions',
        'total_deductions',
        'net_salary',
        'payment_method',
        'paid_at',
        'paid_via_voucher_id',
    ];

    protected function casts(): array
    {
        return [
            'deductions' => 'json',
            'gross_salary' => 'decimal:2',
            'total_deductions' => 'decimal:2',
            'net_salary' => 'decimal:2',
            'paid_at' => 'datetime',
        ];
    }

    public function payrollPeriod(): BelongsTo
    {
        return $this->belongsTo(PayrollPeriod::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function salaryStructure(): BelongsTo
    {
        return $this->belongsTo(SalaryStructure::class);
    }

    public function paidViaVoucher(): BelongsTo
    {
        return $this->belongsTo(ExpenseVoucher::class, 'paid_via_voucher_id');
    }
}
