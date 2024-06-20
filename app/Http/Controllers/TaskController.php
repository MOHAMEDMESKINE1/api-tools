<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Task;
use Illuminate\Http\Request;
use App\Http\Requests\TaskRequest;
use App\Http\Resources\TaskResource;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        if ($query) {
            $tasks = Task::where('title', 'LIKE', "%{$query}%")
                ->orWhere('pin', 'LIKE', "%{$query}%")

                ->get();
        } else {
            $tasks = Task::all();
        }

        return TaskResource::collection($tasks);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskRequest $request)
    {
        try {
            $task = Task::create($request->validated());
            return new TaskResource($task);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskRequest $request, Task $task)
    {
        try {
            $task->update($request->validated());

            return new TaskResource($task);
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        try {
            $task->delete();

            return response()->noContent();
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }
}
