<?php

namespace App\Http\Controllers;

use App\Models\Resource;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ResourceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Resource::orderBy('category')->get();
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
        $resource=Resource::create($validated);
        return ['redirect' => "/resources/$resource->id", 'success' => true];
    }

    /**
     * Display the specified resource.
     */
    public function show(Resource $resource)
    {
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
                'price_in_cents' => ['required', 'integer', 'min:0'],
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
