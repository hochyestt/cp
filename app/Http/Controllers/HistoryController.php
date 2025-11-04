<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return Inertia::render('History', [
            'tasks' => $user->tasks()->orderBy('created_at', 'desc')->get(),
            'habits' => $user->habits()->orderBy('created_at', 'desc')->get(),
        ]);
    }
}
