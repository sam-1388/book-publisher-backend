<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEmployeeRequest;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->query('search')) {
            $name = $request->query('search');
            /* $temp = Occupation::with(['employees' => function ($query) use ($name) {
                $query->whereLike('name', "$name%");
            }])->get();
            $occupations = $temp->reject(function (Occupation $occ) {
                return $occ->employees->isEmpty();
            })->values();
            foreach ($occupations as $occupation) {
                foreach ($occupation->employees as $employee) {
                    $employee->image = route('employeeImage', [$employee->id]);
                }
            } */
            $employees = $request->user()->employees()->whereLike('name', "$name%")->get();
            if ($employees->isEmpty()) {
                return [];
            }
            $occupations = $request->user()->occupations()->with(['employees' => function ($query) use ($name) {
                $query->whereLike('name', "%$name%");
            }])->whereAttachedTo($employees)->get();
            foreach ($occupations as $occupation) {
                foreach ($occupation->employees as $employee) {
                    $employee->image = $employee->image ? route('employeeImage', [$employee->id]) : null;
                }
            }
            return $occupations;
        }
        if ($request->query('occupation')) {
            $requestedOccupation = $this->getCorrectType($request->query('occupation'));
            $actualOccupation = $request->user()->occupations()->where('name', $requestedOccupation)->firstOrFail();
            $employees = $actualOccupation->employees;
            foreach ($employees as $employee) {
                $employee->image = $employee->image ? route('employeeImage', [$employee->id]) : null;
            }
            return ['occupation' => $actualOccupation];
        }


        $occupations =  $request->user()->occupations()->with('employees')->get();
        foreach ($occupations as $occupation) {
            foreach ($occupation->employees as $employee) {
                $employee->image = $employee->image ? route('employeeImage', [$employee->id]) : null;
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
            case 'printing':
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


        $employee = $request->user()->employees()->create($data);
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
            'image' => $employee->image ? route('employeeImage', [$employee->id]) : null,
            'occupationNames' => $employee->occupations()->pluck('name'),
        ], 200);
    }

    public function getImage(Employee $employee)
    {
        if ($employee->image) {
            return Storage::disk('local')->response($employee->image);
        }
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
