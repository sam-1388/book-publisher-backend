<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Employee::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {   
        $path=Storage::disk('public')->putFile('/employees',request('image'),'private');

        $data=[
            ...$request->safe()->except('image','occupations'),
            'image'=>$path
            ];  

        $employee=Employee::create($data);
        foreach ($request->validated('occupations')->occupations as $id) {
            $employee->occupations()->save(
                Occupation::findOrFail($id)
            );
        }

        return response(['success'=>true],200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {
        return ['employee'=>$employee];
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $path=Storage::disk('public')->putFile('/employees',request('image'),'private');
        
        $data=[
            ...$request->safe()->except('image'),
            'image'=>$path
            ];  
            
        $employee->update($data);        
        return response(['success'=>true],200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->deleteOrFail();
        return response(['usccess'=>true],200);
    }
}
