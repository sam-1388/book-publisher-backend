<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Book;
use App\Models\Employee;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Task::all();
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {

            $data = $request->validate([
                'name' => ['required'],
                'type' => [Rule::in(['translation', 'proofReading', 'copyEditing', 'typeSetting', 'printing']), 'required'],
                'deadline' => [Rule::dateTime()->after(today())->format('Y-m-d')],
                'pagesStart' => ['nullable', 'number', Rule::numeric()->min(1)],
                'pagesEnd' => ['nullable', 'number', Rule::numeric()->min(1)],
                'bookId' => [Rule::exists('books', 'id')],
                'employeeId' => [Rule::exists('employee', 'id')],
                'notes' => [Rule::string()->max(255)]
            ]);


            $task = Task::create($request->except(['bookId', 'employeeId']));
            $task->book()->associate(Book::find($data['bookId']));
            $task->employee()->associate(Employee::find($data['employeeId']));

            $task->save();
            return response(['redirect' => "tasks/$task->id", 'success' => true], 200);
        } catch (ValidationException $th) {
            return response(
                $th->errors(),
                422
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return $task;
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        $task->delete();
        return response(['success' => true], 200);
    }
}
