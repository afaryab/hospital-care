<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'tr_number' => ['nullable', 'string', 'max:255'],
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'type' => ['nullable', 'string', 'max:255'],
            'income_or_expense' => ['nullable', 'in:INCOME,EXPENSE'],
            'amount_min' => ['nullable', 'numeric', 'min:0'],
            'amount_max' => ['nullable', 'numeric', 'min:0'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = Transaction::query()
            ->with(['patient'])
            ->latest('id');

        if (!empty($filters['tr_number'])) {
            $query->where('tr_number', 'like', "%{$filters['tr_number']}%");
        }

        if (!empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['income_or_expense'])) {
            $query->where('income_or_expense', $filters['income_or_expense']);
        }

        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        if (!empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (!empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        $exact = collect();

        if (!empty($filters['tr_number'])) {
            $exact = Transaction::query()
                ->with(['patient'])
                ->where('tr_number', $filters['tr_number'])
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

    public function store(Request $request)
    {
        $data = $request->validate([
            'tr_number' => ['nullable', 'string', 'max:255', Rule::unique('transactions', 'tr_number')],
            'old_id' => ['nullable', 'integer'],
            'closing_id' => ['nullable', 'integer', 'exists:closings,id'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'panel_id' => ['nullable', 'integer', 'exists:panels,id'],
            'receaveable_id' => ['nullable', 'integer', 'exists:receaveables,id'],
            'type' => ['required', 'string', 'max:255'],
            'income_or_expense' => ['required', 'in:INCOME,EXPENSE'],
            'amount' => ['required', 'numeric', 'min:0'],
            'amount_alphabetical' => ['nullable', 'string', 'max:255'],
            'orignal_amount' => ['nullable', 'numeric', 'min:0'],
            'customer_payed' => ['nullable', 'numeric', 'min:0'],
            'change' => ['nullable', 'numeric'],
            'edited_amount' => ['nullable', 'numeric'],
            'is_refunded' => ['nullable', 'boolean'],
        ]);

        $transaction = Transaction::create($data);

        return response()->json([
            'message' => 'Transaction created successfully.',
            'data' => $transaction->load(['patient']),
        ], 201);
    }

    public function show(Transaction $transaction)
    {
        return response()->json([
            'data' => $transaction->load(['patient', 'elements']),
        ]);
    }

    public function update(Request $request, Transaction $transaction)
    {
        $data = $request->validate([
            'tr_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('transactions', 'tr_number')->ignore($transaction->id)],
            'old_id' => ['nullable', 'integer'],
            'closing_id' => ['nullable', 'integer', 'exists:closings,id'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'panel_id' => ['nullable', 'integer', 'exists:panels,id'],
            'receaveable_id' => ['nullable', 'integer', 'exists:receaveables,id'],
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'income_or_expense' => ['sometimes', 'required', 'in:INCOME,EXPENSE'],
            'amount' => ['sometimes', 'required', 'numeric', 'min:0'],
            'amount_alphabetical' => ['nullable', 'string', 'max:255'],
            'orignal_amount' => ['nullable', 'numeric', 'min:0'],
            'customer_payed' => ['nullable', 'numeric', 'min:0'],
            'change' => ['nullable', 'numeric'],
            'edited_amount' => ['nullable', 'numeric'],
            'is_refunded' => ['nullable', 'boolean'],
        ]);

        $transaction->update($data);

        return response()->json([
            'message' => 'Transaction updated successfully.',
            'data' => $transaction->load(['patient']),
        ]);
    }

    public function destroy(Transaction $transaction)
    {
        $transaction->delete();

        return response()->json([
            'message' => 'Transaction deleted successfully.',
        ]);
    }
}
