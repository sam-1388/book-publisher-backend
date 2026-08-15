<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Book;
use App\Models\Employee;
use App\Rules\OneEmployeeOneTask;
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
                'page_start' => ['nullable', Rule::numeric()->min(1)],
                'page_end' => ['nullable',  Rule::numeric()->min(1)->greaterThan('page_start')],
                'book_id' => [Rule::exists('books', 'id')],
                'employee_id' => [new OneEmployeeOneTask],
                'notes' => [Rule::string()->max(255)]
            ]);


            $task = Task::create($request->all());

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
        if ($task->page_start === null && $task->page_end === null) {
            $taskSize = 'the full book';
        } elseif ($task->page_start !== null && $task->page_end === null) {
            $taskSize = "from page {$task->page_start} to the end of the book";
        } elseif ($task->page_start === null && $task->page_end !== null) {
            $taskSize = "from the beginning of the book to page {$task->page_end}";
        } else {
            $taskSize = "from page {$task->page_start} to page {$task->page_end}";
        }
        $task->fill(['task_size' => $taskSize]);
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
