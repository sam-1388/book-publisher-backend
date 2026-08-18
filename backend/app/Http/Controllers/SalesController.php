<?php

namespace App\Http\Controllers;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
   /*  public function index(Request $request)

    {
        try {

            $filter = $request->validate([
                'unit' => ['sometimes', 'required', 'in:day,week,month,year'],
                'qunatity' => ['sometimes', 'requrired', 'integer']
            ]);
            $date = Carbon::now()->diff
            $orders = Auth::user()->orders
                ->query()
                ->where('status', 'done')
                ->latest('updated_at')->when($filter['unit'],function(Builder $query , $filter['unit']){
                        $query->whereDate('updated_at','>',);
                    }
                );
        } catch (\Throwable $th) {
            //throw $th;
        }
    } */

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
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
