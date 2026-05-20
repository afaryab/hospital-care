<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceOrder;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceOrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'so_number' => ['nullable', 'string', 'max:255'],
            'patient_id' => ['nullable', 'integer', 'exists:patients,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'service_id' => ['nullable', 'integer', 'exists:services,id'],
            'type' => ['nullable', 'string', 'max:255'],
            'created_from' => ['nullable', 'date'],
            'created_to' => ['nullable', 'date'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:50'],
        ]);

        $query = ServiceOrder::query()
            ->with(['patient', 'doctor', 'service'])
            ->latest('id');

        if (! empty($filters['so_number'])) {
            $term = $filters['so_number'];
            $query->where(function ($q) use ($term) {
                $q->where('so_number', 'like', "%{$term}%")
                    ->orWhere('so_short', 'like', "%{$term}%");
            });
        }

        if (! empty($filters['patient_id'])) {
            $query->where('patient_id', $filters['patient_id']);
        }

        if (! empty($filters['doctor_id'])) {
            $query->where('doctor_id', $filters['doctor_id']);
        }

        if (! empty($filters['service_id'])) {
            $query->where('service_id', $filters['service_id']);
        }

        if (! empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (! empty($filters['created_from'])) {
            $query->whereDate('created_at', '>=', $filters['created_from']);
        }

        if (! empty($filters['created_to'])) {
            $query->whereDate('created_at', '<=', $filters['created_to']);
        }

        $exact = collect();

        if (! empty($filters['so_number'])) {
            $term = $filters['so_number'];
            $exact = ServiceOrder::query()
                ->with(['patient', 'doctor', 'service'])
                ->where(function ($q) use ($term) {
                    $q->where('so_number', $term)->orWhere('so_short', $term);
                })
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
            'type' => ['required', 'string', 'max:255'],
            'token' => ['nullable', 'string', 'max:255'],
            'so_number' => ['required', 'string', 'max:255', Rule::unique('service_orders', 'so_number')],
            'so_short' => ['nullable', 'string', 'max:255'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'patient_id' => ['required', 'integer', 'exists:patients,id'],
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'service_recestation_id' => ['nullable', 'integer', 'exists:service_recestations,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_composit' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'notes_json' => ['nullable', 'array'],
            'payee_type' => ['nullable', 'string', 'max:255'],
            'payee_id' => ['nullable', 'integer'],
        ]);

        $serviceOrder = ServiceOrder::create($data);

        return response()->json([
            'message' => 'Service order created successfully.',
            'data' => $serviceOrder->load(['patient', 'doctor', 'service']),
        ], 201);
    }

    public function show(ServiceOrder $serviceOrder)
    {
        return response()->json([
            'data' => $serviceOrder->load(['patient', 'doctor', 'service']),
        ]);
    }

    public function update(Request $request, ServiceOrder $serviceOrder)
    {
        $data = $request->validate([
            'type' => ['sometimes', 'required', 'string', 'max:255'],
            'token' => ['nullable', 'string', 'max:255'],
            'so_number' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('service_orders', 'so_number')->ignore($serviceOrder->id)],
            'so_short' => ['nullable', 'string', 'max:255'],
            'created_by' => ['nullable', 'integer', 'exists:users,id'],
            'patient_id' => ['sometimes', 'required', 'integer', 'exists:patients,id'],
            'service_id' => ['sometimes', 'required', 'integer', 'exists:services,id'],
            'service_recestation_id' => ['nullable', 'integer', 'exists:service_recestations,id'],
            'doctor_id' => ['nullable', 'integer', 'exists:users,id'],
            'is_composit' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string'],
            'notes_json' => ['nullable', 'array'],
            'payee_type' => ['nullable', 'string', 'max:255'],
            'payee_id' => ['nullable', 'integer'],
        ]);

        $serviceOrder->update($data);

        return response()->json([
            'message' => 'Service order updated successfully.',
            'data' => $serviceOrder->load(['patient', 'doctor', 'service']),
        ]);
    }

    public function destroy(ServiceOrder $serviceOrder)
    {
        $serviceOrder->delete();

        return response()->json([
            'message' => 'Service order deleted successfully.',
        ]);
    }

    public function completedUnpaid(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
            'doctor_only' => ['nullable', 'boolean'],
            'payed_to' => ['nullable', 'integer', 'exists:users,id'],
            'closing_id' => ['nullable', 'integer', 'exists:closings,id'],
        ]);

        $query = ServiceOrder::query()
            ->with(['patient', 'doctor', 'service'])
            ->where('status', 'CLOSED')
            ->latest('id');

        if (! empty($filters['payed_to'])) {
            $query->whereDoesntHave('expenseVouchers', fn ($q) => $q->where('payed_to', $filters['payed_to']));
        } else {
            $query->whereDoesntHave('expenseVouchers');
        }

        if (! empty($filters['doctor_only']) && ! empty($filters['payed_to'])) {
            $query->where('doctor_id', $filters['payed_to']);
        }

        if (! empty($filters['closing_id'])) {
            $closingId = $filters['closing_id'];
            $query->whereHas('transactionElements', fn ($q) => $q->where('closing_id', $closingId));
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('so_number', 'like', "%{$search}%")
                    ->orWhere('so_short', 'like', "%{$search}%")
                    ->orWhereHas('patient', fn ($pq) => $pq->where('name', 'like', "%{$search}%"));
            });
        }

        return response()->json([
            'data' => $query->limit($filters['limit'] ?? 25)->get(),
        ]);
    }
}
