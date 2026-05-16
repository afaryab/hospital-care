<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Icd10Code;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class Icd10CodeController extends Controller
{
    /**
     * Return active ICD-10 codes, optionally filtered by search term.
     * Used by treatment record forms on the frontend.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Icd10Code::where('is_active', true)
            ->orderBy('code');

        if ($search = $request->input('q')) {
            $query->where(function ($q) use ($search) {
                $q->where('code', 'like', $search.'%')
                    ->orWhere('description', 'like', '%'.$search.'%');
            });
        }

        if ($category = $request->input('category')) {
            $query->where('category', $category);
        }

        $codes = $query->limit(50)->get(['id', 'code', 'description', 'category']);

        return response()->json(['data' => $codes]);
    }
}
