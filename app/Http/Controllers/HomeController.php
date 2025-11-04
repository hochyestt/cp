<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Task;
use App\Models\Habit;

class HomeController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        $latestTasks = $user->tasks()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $latestHabits = $user->habits()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $totalStats = [
            'total_tasks_count' => $user->tasks()->count(),
            'total_habits_count' => $user->habits()->count(),
            'completed_tasks_count' => $user->tasks()->where('status', 'completed')->count(),
            'total_habits_done' => $user->habits()->sum('times_done_since_reset') ?? 0, 
        ];
        
        
        return Inertia::render('Home', [
            'latestTasks' => $latestTasks, 
            'latestHabits' => $latestHabits,
            'stats' => $totalStats,
        ]);
    }
}