<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $ordering = $request->query('orderBy');
        $filtering = $request->query('filterBy');

        if ($ordering) {
            if ($ordering == 'highestCost') {
                $resources = Resource::orderBy('price_in_cents', 'desc')->get();
            } else if ($ordering == 'highestStock') {
                $resources = Resource::orderBy('stock', 'desc')->get();
            }
        } else if ($filtering) {
            if ($filtering == 'lowStock') {
                $resources = Resource::where('status', 'low stock')->get();
            } else if ($filtering == 'outOfStock') {
                $resources = Resource::where('status', 'out of stock')->get();
            }
        } else {

            $resources = Resource::orderBy('category')->get();
        }

        $sum = 0;
        foreach ($resources as $resource) {
            $cost = (int) $resource->price_in_cents;
            $sum += $cost * $resource->stock;
            $resource->price_in_cents = $this->moneyConvert($resource->price_in_cents);
        }
        $totalStock = DB::table('resources')->sum('stock');
        $lowStockCount = DB::table('resources')->where('status', 'low stock')->count();
        $outOfStockCount = DB::table('resources')->where('status', 'out of stock')->count();

        return [
            'resources' => $resources,
            'totalCost' => $this->moneyConvert($sum),
            'totalStock' => $totalStock,
            'lowStockCount' => $lowStockCount,
            'outOfStockCount' => $outOfStockCount

        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'category' => ['required', 'string', 'max:255'],
                'stock' => ['required', 'integer', 'min:0'],
                'unit' => ['nullable', 'string', 'in:piece,pack,box,kg,g,liter,ml,bottle,container,ream'],
                'min_stock' => ['required', 'integer', 'min:0',],
                'price_in_cents' => ['required', 'numeric', 'min:0'],
                'supplier' => ['nullable', 'string']


            ]);
        } catch (ValidationException $th) {
            return response($th->errors(), 422);
        }
        $stock = $validated['stock'];
        $min_stock = $validated['min_stock'];

        $validated['status'] = match (true) {
            $stock > $min_stock => 'in stock',
            $stock <= $min_stock && $stock != 0 => 'low stock',
            $stock == 0 => 'out of stock',
        };

        $validated['price_in_cents'] = (int) round($validated['price_in_cents'] * 100);

        $resource = Resource::create($validated);

        return ['redirect' => "/resources/$resource->id", 'success' => true];
    }

    private function moneyConvert(int $x): string
    {
        return '$' . (number_format($x / 100, 2));
    }
    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
        $resource->fill([
            'total_value' => $this->moneyConvert(((int)$resource->price_in_cents) * ($resource->stock))
        ]);
        $resource->price_in_cents = $this->moneyConvert($resource->price_in_cents);
        return $resource;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Resource $resource)
    {
        try {

            $validated = $request->validate([
                'name' => ['required', 'string', 'max:255'],
                'category' => ['required', 'string', 'max:255'],
                'stock' => ['required', 'integer', 'min:0'],
                'unit' => ['nullable', 'string', 'in:piece,pack,box,kg,g,liter,ml,bottle,container,ream'],
                'min_stock' => ['required', 'integer', 'min:0',],
                'price_in_cents' => ['required', 'numeric', 'min:0'],
                'supplier' => ['nullable', 'string']

            ]);
        } catch (ValidationException $th) {
            return response($th->errors(), 422);
        }
        $stock = $validated['stock'];
        $min_stock = $validated['min_stock'];
        $validated['status'] = match (true) {
            $stock > $min_stock => 'in stock',
            $stock <= $min_stock && $stock != 0 => 'low stock',
            $stock == 0 => 'out of stock',
        };
        $validated['price_in_cents'] = (int) round($validated['price_in_cents'] * 100);
        $resource->update($validated);
        return ['redirect' => "/resources/$resource->id", 'success' => true];
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Resource $resource)
    {
        $resource->delete();
        return ['success' => true];
    }
}
