<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Closing;
use Illuminate\Http\Request;

class ClosingController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'string', 'in:open,closed'],
            'limit' => ['nullable', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Closing::query()
            ->select(['id', 'ct_number', 'status', 'created_at'])
            ->latest('id');

        if (! empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (! empty($filters['search'])) {
            $search = $filters['search'];
            $query->where('ct_number', 'like', "%{$search}%");
        }

        return response()->json([
            'data' => $query->limit($filters['limit'] ?? 50)->get(),
        ]);
    }
}
