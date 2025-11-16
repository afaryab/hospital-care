<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExpenseVoucher;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Svg\Tag\Rect;

class ExpenseVoucherController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try{
            $query = ExpenseVoucher::query()->orderBy('created_at','DESC')->where('id','!=', null);

            $exactMatches = [];

            // Check if the request have MR Number
            $mrNumber = $request->get('vc_number', false);

            if($mrNumber){
                if(Str::length($mrNumber) === 15){
                    $exactMatches[] = ExpenseVoucher::where(['vc_number' => $mrNumber])->first();
                }
                
                $query->where('vc_number', 'LIKE', "{$mrNumber}%");
            }

            if(count($exactMatches) > 0){
                $query->whereNotIn('id', array_map(function($item){
                    return $item;
                }, collect($exactMatches)->pluck('id')->toArray()));
            }


            return response()->json([
                "data" => [
                    "exact" => $exactMatches,
                    "possible" => $query->limit(3)->get()
                ]
            ]);
        }catch(\Exception $e){
            return response()->json([
                "message" => "An error occurred while fetching expense vouchers.",
                "error" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Service $service)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Service $service)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Service $service)
    {
        //
    }
}
