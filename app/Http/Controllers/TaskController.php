<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class TaskController extends BaseController
{
    use AuthorizesRequests;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function index()
    {
        try {
            $tasks = Auth::user()->tasks;

            return response()->json(['data' => $tasks]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch tasks: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch tasks'], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'nullable|string|max:1000',
                'completed' => 'nullable|boolean',
            ]);

            $task = Auth::user()->tasks()->create($validated);

            return response()->json([
                'message' => 'Task created successfully',
                'data' => $task
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to create task: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create task'], 500);
        }
    }

    public function show(Task $task)
    {
        try {
            if ($task->user_id !== Auth::id()) {
                return response()->json(['error' => 'You are not authorized to view this task'], 403);
            }

            return response()->json(['data' => $task]);
        } catch (\Exception $e) {
            Log::error('Failed to fetch task: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to fetch task'], 500);
        }
    }

    public function update(Request $request, Task $task)
    {
        try {
            $this->authorize('update', $task);

            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'nullable|string|max:1000',
                'completed' => 'nullable|boolean',
            ]);

            $task->update($validated);

            return response()->json([
                'message' => 'Task updated successfully',
                'data' => $task->fresh()
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Failed to update task: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to update task'], 500);
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->authorize('delete', $task);

            $task->delete();

            return response()->json(['message' => 'Task deleted successfully']);
        } catch (\Exception $e) {
            Log::error('Failed to delete task: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to delete task'], 500);
        }
    }
}
