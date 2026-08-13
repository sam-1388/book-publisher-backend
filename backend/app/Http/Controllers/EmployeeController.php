<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

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
        if ($request->file('image')) {

            $path = Storage::disk('local')->putFile('/employees', request('image'));
            $data = [
                ...$request->safe()->except('image', 'selectedOccupations'),
                'image' => $path
            ];
        } else {
            $data = [
                ...$request->safe()->except('image', 'selectedOccupations'),
                'image' => null
            ];
        }



        $employee = Employee::create($data);
        foreach ($request->validated('selectedOccupations') as $id) {
            $temp = Occupation::findOrFail($id);
            $employee->occupations()->save($temp);
        }

        return response(['redirect' => "/employees/$employee->id", 'success' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {


        return response([
            ...$employee->except(['image']),
            'image' => route('employeeImage', [$employee->id])
        ], 200);
    }

    public function getImage(Employee $employee)
    {
        $image = Storage::disk('local')->get($employee->image);
        if ($image == null) {
            return response(status: 404);
        }
        $mime = Storage::disk('local')->mimeType($employee->image);
        $filename = basename($employee->image);
        return response($image, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', "inline;filename=$filename");
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        $path = Storage::disk('public')->putFile('/employees', request('image'), 'private');

        $data = [
            ...$request->safe()->except('image'),
            'image' => $path
        ];

        $employee->update($data);
        return response(['success' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        $employee->deleteOrFail();
        return response(['usccess' => true], 200);
    }
}
