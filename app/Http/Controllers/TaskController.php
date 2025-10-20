<?php

namespace App\Http\Controllers;

use App\Http\Requests\Task\CreateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    public function store(CreateRequest $request)
    {
        $userInput = $request->validated();

        $task = Task::create([
            'title' => $userInput['title'],
            'description' => $userInput['description'],
            'status' => 1,
        ]);

        return new TaskResource($task);
    }

    public function index(Request $request)
    {
        $tasks = Task::orderByDesc('id');

        return TaskResource::apiPaginate($tasks, $request);
    }

    public function destroy($id)
    {
        $task = Task::find($id);
        if (!$task) {
            return response()->json([
                'message' => 'Task not found',
            ], 404);
        }
        $task->delete();
        return response()->json([
            'message' => 'Delete success',
            'data' => new TaskResource($task),
        ]);
    }
}
