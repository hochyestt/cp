<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    // Показать все цели
    public function index()
    {
        return response()->json(Goal::all());
    }

    public function store(Request $request)
    {
        $goal = Goal::create($request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]));

        return response()->json($goal, 201);
    }

    public function show(Goal $goal)
    {
        return response()->json($goal);
    }

    public function update(Request $request, Goal $goal)
    {
        $goal->update($request->only('title', 'description'));
        return response()->json($goal);
    }

    public function destroy(Goal $goal)
    {
        $goal->delete();
        return response()->json(null, 204);
    }
}
