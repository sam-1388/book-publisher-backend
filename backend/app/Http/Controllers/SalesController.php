<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SalesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)

    {
        try {

            $filter = $request->validate([
                'unit' => ['sometimes', 'required', 'in:day,week,month,year'],
                'quantity' => ['sometimes', 'required', 'integer', 'min:1']
            ]);
        } catch (ValidationException $th) {
            return response(
                $th->errors(),
                422
            );
        }
        $unit = $filter['unit'] ?? null;
        $quantity = $filter['quantity'] ?? null;

        $orders = Auth::user()
            ->orders()
            ->where('status','=','done')
            ->when(
                $unit && $quantity,
                function (Builder $query) use ($quantity, $unit) {
                    $query->whereDate('updated_at', '>', Carbon::parse("-$quantity $unit"));
                })
            ->latest('updated_at')
            ->get();
            
        $sum = $orders->sum('final_price_in_cents');
        $count = $orders->count();
        $sumInDollars = number_format($sum / 100, 2) . '$';
        return ['orders' => $orders, 'sum' => $sumInDollars, 'count' => $count];
    }
}
