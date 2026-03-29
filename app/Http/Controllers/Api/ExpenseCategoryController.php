<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ExpenseCategoryController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'type' => ['nullable', 'string', 'max:255'],
            'pay_doc' => ['nullable', 'boolean'],
            'pay_others' => ['nullable', 'boolean'],
            'pay_users' => ['nullable', 'boolean'],
            'pay_patient' => ['nullable', 'boolean'],
            'allow_petty_cash' => ['nullable', 'boolean'],
            'allow_voucher' => ['nullable', 'boolean'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = ExpenseCategory::query()->whereNotIn('name', [
            'Outdoor Doctors Payments',
            'Indoor Doctors Payments',
        ])->latest('id');

        if (! empty($filters['name'])) {
            $query->where('name', 'like', "%{$filters['name']}%");
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (array_key_exists('pay_doc', $filters)) {
            $query->where('pay_doc', $filters['pay_doc']);
        }

        if (array_key_exists('pay_others', $filters)) {
            $query->where('pay_others', $filters['pay_others']);
        }

        if (array_key_exists('pay_users', $filters)) {
            $query->where('pay_users', $filters['pay_users']);
        }

        if (array_key_exists('pay_patient', $filters)) {
            $query->where('pay_patient', $filters['pay_patient']);
        }

        if (array_key_exists('allow_petty_cash', $filters)) {
            $query->where('allow_petty_cash', $filters['allow_petty_cash']);
        }

        if (array_key_exists('allow_voucher', $filters)) {
            $query->where('allow_voucher', $filters['allow_voucher']);
        }

        $exact = collect();

        if (! empty($filters['name'])) {
            $exact = ExpenseCategory::query()
                ->where('name', $filters['name'])
                ->get();

            if ($exact->isNotEmpty()) {
                $query->whereNotIn('id', $exact->pluck('id'));
            }
        }

        return response()->json([
            'data' => [
                'exact' => $exact->values(),
                'possible' => $query->limit($filters['limit'] ?? 30)->get(),
            ],
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'old_id' => ['nullable', 'integer'],
            'name' => ['required', 'string', 'max:255', Rule::unique('expense_categories', 'name')],
            'type' => ['nullable', 'string', 'max:255'],
            'pay_doc' => ['nullable', 'boolean'],
            'pay_others' => ['nullable', 'boolean'],
            'pay_users' => ['nullable', 'boolean'],
            'pay_patient' => ['nullable', 'boolean'],
            'allow_petty_cash' => ['nullable', 'boolean'],
            'allow_voucher' => ['nullable', 'boolean'],
        ]);

        $category = ExpenseCategory::create($data);

        return response()->json([
            'message' => 'Expense category created successfully.',
            'data' => $category,
        ], 201);
    }

    public function show(ExpenseCategory $expenseCategory)
    {
        return response()->json([
            'data' => $expenseCategory,
        ]);
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $data = $request->validate([
            'old_id' => ['nullable', 'integer'],
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('expense_categories', 'name')->ignore($expenseCategory->id)],
            'type' => ['nullable', 'string', 'max:255'],
            'pay_doc' => ['nullable', 'boolean'],
            'pay_others' => ['nullable', 'boolean'],
            'pay_users' => ['nullable', 'boolean'],
            'pay_patient' => ['nullable', 'boolean'],
            'allow_petty_cash' => ['nullable', 'boolean'],
            'allow_voucher' => ['nullable', 'boolean'],
        ]);

        $expenseCategory->update($data);

        return response()->json([
            'message' => 'Expense category updated successfully.',
            'data' => $expenseCategory,
        ]);
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $expenseCategory->delete();

        return response()->json([
            'message' => 'Expense category deleted successfully.',
        ]);
    }
}
