<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Task;
use App\Models\Habit;
use App\Models\Statistic;
use Carbon\Carbon;



class DashboardController extends Controller
{
  
    public function index()
    {
        return view('dashboard');
    }

    public function getData()
    {
        $user = Auth::user();
        $today = Carbon::today();

        $tasks = $user->tasks()
                      ->where('completed', false)
                      ->orderBy('due_date')
                      ->limit(5)
                      ->get(['id', 'title', 'due_date']);

        $habits = $user->habits()
                       ->limit(5)
                       ->get(['id', 'title', 'completed_today']);

        $lastWeekStats = $user->statistics()
                              ->where('date', '>=', $today->subDays(7))
                              ->orderBy('date', 'desc')
                              ->first();

        $statisticsData = [
            'tasks_completed' => $lastWeekStats ? $lastWeekStats->tasks_completed : 0,
            'habits_completed' => $lastWeekStats ? $lastWeekStats->habits_completed : 0,
            'success_percentage' => $lastWeekStats ? $lastWeekStats->success_percentage : 0,
        ];

        return response()->json([
            'user' => [
                'name' => $user->name,
            ],
            'tasks' => $tasks,
            'habits' => $habits,
            'statistics' => $statisticsData,
        ]);
    }
}