<?php

namespace App\Http\Controllers\Api;

use App\Helpers\DateHelper;
use App\Http\Controllers\Controller;
use App\Models\ExpenseVoucher;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $filters = $request->validate([
            'vc_number' => ['nullable', 'string', 'max:255'],
            'exp_category_id' => ['nullable', 'integer', 'exists:expense_categories,id'],
            'service_order_id' => ['nullable', 'integer', 'exists:service_orders,id'],
            'payed_to' => ['nullable', 'integer', 'exists:users,id'],
            'payed_to_name' => ['nullable', 'string', 'max:255'],
            'amount_min' => ['nullable', 'numeric', 'min:0'],
            'amount_max' => ['nullable', 'numeric', 'min:0'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = ExpenseVoucher::query()
            ->with(['expCategory', 'serviceOrder', 'payedTo'])
            ->latest('id');

        if (! empty($filters['vc_number'])) {
            $query->where('vc_number', 'like', "%{$filters['vc_number']}%");
        }

        if (! empty($filters['exp_category_id'])) {
            $query->where('exp_category_id', $filters['exp_category_id']);
        }

        if (! empty($filters['service_order_id'])) {
            $query->where('service_order_id', $filters['service_order_id']);
        }

        if (! empty($filters['payed_to'])) {
            $query->where('payed_to', $filters['payed_to']);
        }

        if (! empty($filters['payed_to_name'])) {
            $query->where('payed_to_name', 'like', "%{$filters['payed_to_name']}%");
        }

        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        if (! empty($filters['created_from'])) {
            $query->where('created_at', '>=', DateHelper::dayStartUtc($filters['created_from']));
        }

        if (! empty($filters['created_to'])) {
            $query->where('created_at', '<=', DateHelper::dayEndUtc($filters['created_to']));
        }

        $exact = collect();

        if (! empty($filters['vc_number'])) {
            $exact = ExpenseVoucher::query()
                ->with(['expCategory', 'serviceOrder', 'payedTo'])
                ->where('vc_number', $filters['vc_number'])
                ->get();

            if ($exact->isNotEmpty()) {
                $query->whereNotIn('id', $exact->pluck('id'));
            }
        }

        return response()->json([
            'data' => [
                'exact' => $exact->values(),
                'possible' => $query->limit($filters['limit'] ?? 10)->get(),
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'old_id' => ['nullable', 'integer'],
            'exp_category_id' => ['required', 'integer', 'exists:expense_categories,id'],
            'service_order_id' => ['nullable', 'integer', 'exists:service_orders,id'],
            'payed_to' => ['nullable', 'integer', 'exists:users,id'],
            'payed_to_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:0'],
            'vc_number' => ['nullable', 'string', 'max:255', Rule::unique('expense_vouchers', 'vc_number')],
        ]);

        $voucher = ExpenseVoucher::create($data);

        return response()->json([
            'message' => 'Expense voucher created successfully.',
            'data' => $voucher->load(['expCategory', 'serviceOrder', 'payedTo']),
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ExpenseVoucher $expenseVoucher)
    {
        return response()->json([
            'data' => $expenseVoucher->load(['expCategory', 'serviceOrder', 'payedTo']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ExpenseVoucher $expenseVoucher)
    {
        $data = $request->validate([
            'old_id' => ['nullable', 'integer'],
            'exp_category_id' => ['sometimes', 'required', 'integer', 'exists:expense_categories,id'],
            'service_order_id' => ['nullable', 'integer', 'exists:service_orders,id'],
            'payed_to' => ['nullable', 'integer', 'exists:users,id'],
            'payed_to_name' => ['nullable', 'string', 'max:255'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'vc_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('expense_vouchers', 'vc_number')->ignore($expenseVoucher->id)],
        ]);

        $expenseVoucher->update($data);

        return response()->json([
            'message' => 'Expense voucher updated successfully.',
            'data' => $expenseVoucher->load(['expCategory', 'serviceOrder', 'payedTo']),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ExpenseVoucher $expenseVoucher)
    {
        $expenseVoucher->delete();

        return response()->json([
            'message' => 'Expense voucher deleted successfully.',
        ]);
    }
}
