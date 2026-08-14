<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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
        $request->validate([
            'name'=>['required'],
            'type'=>[Rule::in(['translation','proofReading','copyEditing','typeSetting','printing']),'requried'],
            'deadline'=>[Rule::dateTime()->after(today())->format('Y-m-d')],
            'pagesStart'=>['number',Rule::numeric()->min(1)],
            'pagesEnd'=>['number',Rule::numeric()->min(1)],
            'bookId'=>[Rule::exists('books','id')],
            'employeeId'=>[Rule::exists('employee','id')],
            'notes'=>[Rule::string()->max(255)]
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Task $task)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        //
    }
}
