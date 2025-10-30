<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class TaskController extends Controller
{
    public function index(): JsonResponse
    {
        $tasks = auth()->user()->tasks;
        return response()->json($tasks);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:45',
            'description' => 'nullable|string|max:45',
            'status' => 'string|max:45',
            'priority' => 'string|max:45',
        ]);

        $task = auth()->user()->tasks()->create($request->only([
            'title', 'description', 'status', 'priority'
        ]));

        // Для Inertia - возвращаем редирект на home с обновленными данными
        return redirect()->route('home');
    }

    public function show($id): JsonResponse
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        return response()->json($task);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        
        $request->validate([
            'title' => 'string|max:45',
            'description' => 'nullable|string|max:45',
            'status' => 'string|max:45',
            'priority' => 'string|max:45',
        ]);

        $task->update($request->all());

        return response()->json($task);
    }

    public function destroy($id): JsonResponse
    {
        $task = auth()->user()->tasks()->findOrFail($id);
        $task->delete();

        return response()->json(null, 204);
    }
}