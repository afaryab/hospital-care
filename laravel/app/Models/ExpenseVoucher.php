<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ExpenseVoucher extends Model
{
    protected $fillable = [
        'vc_number',
        'old_id',
        'exp_category_id',
        'service_order_id',
        'payed_to',
        'amount',
        'created_at',
        'updated_at'
    ];


    /**
     * Generate a unique expense voucher number.
     *
     * @return string
     */
    public function generateExpenseVoucherNumber(): string
    {
        // Example logic: "EV-" followed by current timestamp and a random number
        return DB::transaction(function () {
            $now = Carbon::now();
            $year = $now->format('Y');
            $month = $now->format('m');

            // Count how many patients have been created this month with PS numbers
            // Use FOR UPDATE to lock the table and prevent race conditions
            $count = self::where('vc_number', 'like', "VC/{$year}/{$month}/%")
                        ->lockForUpdate()
                        ->count();
            $count += 1; // Increment for the new patient

            // Pad the count to be 4 digits
            $count = str_pad($count, 4, '0', STR_PAD_LEFT);

            return "VC/{$year}/{$month}/{$count}";
        });
    }
}
