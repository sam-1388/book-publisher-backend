<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use App\Models\Occupation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('occupation')) {
            $requestedOccupation = $this->getCorrectType($request->query('occupation'));
            $actualOccupation = Occupation::where('name', $requestedOccupation)->firstOrFail();
            $employees = $actualOccupation->employees;
            foreach ($employees as $employee) {
                $employee->image = route('employeeImage', [$employee->id]);
            }
            return ['occupation' => $actualOccupation];
        }


        $occupations =  Occupation::with('employees')->get();
        foreach ($occupations as $occupation) {
            foreach ($occupation->employees as $employee) {
                $employee->image = route('employeeImage', [$employee->id]);
            }
        }
        return $occupations;
    }
    private function getCorrectType(string $x)
    {
        switch ($x) {
            case 'translation':
                return 'translator';
            case 'copyEditing':
                return 'copyEditor';
            case 'typeSetting':
                return 'typeSetter';
            case 'proofReading':
                return 'proofReader';
            case 'ready for printing':
                return 'printer';

            default:
                return 'others';
        }
    }
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEmployeeRequest $request)
    {
        if ($request->hasFile('image')) {

            $path = Storage::disk('local')->putFile('employees', request('image'));
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
        $employee->occupations()->attach(request('selectedOccupations'));


        return response(['redirect' => "/employees/$employee->id", 'success' => true], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(Employee $employee)
    {


        return response([
            ...$employee->except(['image']),
            'selectedOccupations' => $employee->occupations,
            'image' => route('employeeImage', [$employee->id]),
            'occupationNames' => $employee->occupations()->pluck('name'),
        ], 200);
    }

    public function getImage(Employee $employee)
    {
        if (!$employee->image) {
            return response('', 404);
        }
        return Storage::disk('local')->response($employee->image);
    }
    /**
     * Update the specified resource in storage.
     */
    public function update(StoreEmployeeRequest $request, Employee $employee)
    {
        if ($request->hasFile('image')) {

            $path = Storage::disk('local')->putFile('employees', request('image'));
            $data = [
                ...$request->safe()->except('image', 'selectedOccupations'),
                'image' => $path
            ];
            if ($employee->image) {
                Storage::disk('local')->delete($employee->image);
            }
        } else {
            $data = [
                ...$request->safe()->except('selectedOccupations'),

            ];
        }

        $employee->occupations()->detach();
        $employee->occupations()->attach(request('selectedOccupations'));


        $employee->update($data);
        return response(['redirect' => "/employees/$employee->id", 'success' => true], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Employee $employee)
    {
        if ($employee->image) {
            Storage::disk('local')->delete($employee->image);
        }
        $employee->occupations()->detach();
        $employee->tasks()->delete();
        $employee->deleteOrFail();
        return response(['success' => true], 200);
    }
}
