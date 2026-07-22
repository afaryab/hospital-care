<?php

namespace App\Services;

use App\Models\ServiceOrder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ServiceOrderMerger
{
    /**
     * Merge a set of duplicate service orders into a primary one.
     *
     * Repoints every FK that references a duplicate to the primary, then
     * soft-deletes the duplicates. All work runs inside a single DB
     * transaction. Returns a summary of how many rows were moved per table
     * plus the list of merged duplicate IDs.
     *
     * @param  Collection<int, ServiceOrder>  $duplicates  service orders being collapsed into $primary
     * @return array<string, mixed>
     */
    public function merge(ServiceOrder $primary, Collection $duplicates, ?string $reason = null): array
    {
        $duplicateIds = $duplicates
            ->reject(fn (ServiceOrder $so) => $so->id === $primary->id)
            ->pluck('id')
            ->values()
            ->all();

        if (empty($duplicateIds)) {
            return [
                'primary_id' => $primary->id,
                'merged_ids' => [],
                'counts' => [],
            ];
        }

        return DB::transaction(function () use ($primary, $duplicateIds, $reason): array {
            $counts = [];

            // Simple repoint tables — no unique constraint on service_order_id.
            $counts['transaction_elements'] = DB::table('transaction_elements')
                ->whereIn('service_order_id', $duplicateIds)
                ->update(['service_order_id' => $primary->id]);

            if (DB::getSchemaBuilder()->hasColumn('transaction_elements', 'expense_service_order_id')) {
                $counts['transaction_elements_expense'] = DB::table('transaction_elements')
                    ->whereIn('expense_service_order_id', $duplicateIds)
                    ->update(['expense_service_order_id' => $primary->id]);
            }

            $counts['expense_vouchers'] = DB::table('expense_vouchers')
                ->whereIn('service_order_id', $duplicateIds)
                ->update(['service_order_id' => $primary->id]);

            $counts['consents'] = DB::table('consents')
                ->whereIn('service_order_id', $duplicateIds)
                ->update(['service_order_id' => $primary->id]);

            $counts['bed_assignments'] = DB::table('bed_assignments')
                ->whereIn('service_order_id', $duplicateIds)
                ->update(['service_order_id' => $primary->id]);

            $counts['service_order_versions'] = DB::table('service_order_versions')
                ->whereIn('service_order_id', $duplicateIds)
                ->update(['service_order_id' => $primary->id]);

            // expense_voucher_service_order pivot has UNIQUE(voucher, so). For
            // any voucher already attached to the primary, drop the duplicate
            // row instead of repointing it (would violate the unique key).
            $primaryVoucherIds = DB::table('expense_voucher_service_order')
                ->where('service_order_id', $primary->id)
                ->pluck('expense_voucher_id')
                ->all();

            $counts['expense_voucher_service_order_dropped'] = DB::table('expense_voucher_service_order')
                ->whereIn('service_order_id', $duplicateIds)
                ->whereIn('expense_voucher_id', $primaryVoucherIds)
                ->delete();

            $counts['expense_voucher_service_order_moved'] = DB::table('expense_voucher_service_order')
                ->whereIn('service_order_id', $duplicateIds)
                ->update(['service_order_id' => $primary->id]);

            // treatment_records.service_order_id is UNIQUE. If primary already
            // has one, leave the duplicate's record where it is (the duplicate
            // SO is being soft-deleted; the row stays for audit). Otherwise
            // move the first duplicate's treatment record onto the primary.
            $primaryHasTreatment = DB::table('treatment_records')
                ->where('service_order_id', $primary->id)
                ->exists();

            if (! $primaryHasTreatment) {
                $candidate = DB::table('treatment_records')
                    ->whereIn('service_order_id', $duplicateIds)
                    ->orderBy('id')
                    ->first();

                if ($candidate) {
                    DB::table('treatment_records')
                        ->where('id', $candidate->id)
                        ->update(['service_order_id' => $primary->id]);
                    $counts['treatment_records_moved'] = 1;
                }
            }

            // Soft-delete the duplicates.
            $now = now();
            $counts['duplicates_soft_deleted'] = DB::table('service_orders')
                ->whereIn('id', $duplicateIds)
                ->whereNull('deleted_at')
                ->update(['deleted_at' => $now, 'updated_at' => $now]);

            activity()
                ->performedOn($primary)
                ->causedBy(auth()->user())
                ->withProperties([
                    'merged_ids' => $duplicateIds,
                    'reason' => $reason,
                    'counts' => $counts,
                ])
                ->log('service_orders_merged');

            return [
                'primary_id' => $primary->id,
                'merged_ids' => $duplicateIds,
                'counts' => $counts,
            ];
        });
    }
}
