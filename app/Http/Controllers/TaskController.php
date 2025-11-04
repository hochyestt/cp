<?php

namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TaskController extends Controller
{
    public function index()
    {
        $tasks = Task::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get(['id', 'title', 'description', 'start_time']); 

        return Inertia::render('Tasks', [
            'tasks' => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:45',
            'description' => 'nullable|string|max:45',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date|after_or_equal:start_time',
        ]);

        auth()->user()->tasks()->create($request->only(['title', 'description', 'start_time', 'end_time']));

        return redirect()->route('home');
    }

    public function destroy(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этой задаче.');
        }

        $task->delete();

return redirect()->back();    }
    
    public function markCompleted(Task $task)
    {
        if ($task->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этой задаче.');
        }
        
        $task->update(['completed' => true]);
        return response()->json(['message' => 'Задача выполнена ✅']);
    }
}