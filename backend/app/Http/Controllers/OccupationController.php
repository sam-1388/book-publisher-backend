<?php

namespace App\Http\Controllers;

use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class OccupationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return Occupation::whereBelongsTo($request->user())->get();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $data = $request->validateWithBag(
                'occupationErrors',
                [
                    'name' => ['required', 'unique:occupations'],
                    'color' => ['required', 'hex_color'],
                ],
                [
                    'name.required' => 'occupation name required',
                    'name.unique' => 'this occupation already exists, add another one',
                    'color.required' => 'select a color',
                    'color.hex_color' => 'enter a valid color'
                ]
            );
            $request->user()->occupations()->create($data);
            
        } catch (ValidationException $x) {
            return response($x->errors(), 422);
        }




        return response(['success' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Occupation $occupation)
    {
        return ['occupation' => $occupation];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Occupation $occupation)
    {
        $data = $request->validate([
            'name' => ['required', 'unique:occupations'],
            'color' => ['required', 'hex_color']
        ]);

        Occupation::update($data);
        return response(['success' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Occupation $occupation)
    {
        DB::transaction(function () use ($occupation) {
            if ($occupation->employees->isNotEmpty()) {
                $occupation->employees()->detach();
            }
            $occupation->delete();
        });
    }
}
