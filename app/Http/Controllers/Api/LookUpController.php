<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\Search\GlobalSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LookUpController extends Controller
{
    public function index(Request $request, GlobalSearchService $search): JsonResponse
    {
        $query = (string) $request->string('q');

        return response()->json([
            'data' => mb_strlen($query) > 100 ? [] : $search->search($request->user(), $query),
        ]);
    }
}
