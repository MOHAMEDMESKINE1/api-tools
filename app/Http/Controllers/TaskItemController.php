<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\TaskItem;
use Illuminate\Http\Request;
use App\Http\Requests\TaskItemRequest;
use App\Http\Resources\TasKItemResource;

class TaskItemController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->input('search');
        if ($query) {

            $taskItems = TaskItem::whereHas('task', function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%");
                $q->orWhere('pin', 'LIKE', "%{$query}%");
            })
            ->orWhere('content', 'LIKE', "%{$query}%")
            ->orWhere('done', 'LIKE', "%{$query}%")
            ->get();
        } else {
            $taskItems = TaskItem::with('task')->get();
        }

        return TasKItemResource::collection($taskItems);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TaskItemRequest $request)
    {
        try {

            $taskItem = TaskItem::create($request->validated());
            return new TasKItemResource($taskItem);

        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(TaskItem $taskItem)
    {
        return new TasKItemResource($taskItem);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TaskItemRequest $request, TaskItem $taskItem)
    {
        try {
            
            $taskItem->update($request->validated());

            return new TasKItemResource($taskItem);

        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(TaskItem $taskItem)
    {
        try {

            $taskItem->delete();

            return response()->noContent();
            
        } catch (Exception $ex) {
            return response()->json($ex->getMessage());
        }
        
    }
}
