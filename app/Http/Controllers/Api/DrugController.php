<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Drug;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DrugController extends Controller
{
    /**
     * Live drug search for prescription autocomplete.
     * GET /api/drugs/search?q=amox&limit=15
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->string('q'));
        $limit = min((int) $request->integer('limit', 15), 50);

        if ($q === '') {
            return response()->json(['data' => []]);
        }

        $drugs = Drug::query()
            ->where('is_active', true)
            ->where(function ($query) use ($q) {
                $query->where('name', 'like', "%{$q}%")
                    ->orWhere('generic_name', 'like', "%{$q}%");
            })
            ->with('category:id,name')
            ->orderByRaw('CASE WHEN name LIKE ? THEN 0 ELSE 1 END', ["{$q}%"])
            ->orderBy('name')
            ->limit($limit)
            ->get([
                'id', 'name', 'generic_name', 'type', 'drug_category_id',
                'strength', 'default_dose', 'default_frequency',
                'default_duration', 'default_route',
            ]);

        return response()->json(['data' => $drugs]);
    }
}
