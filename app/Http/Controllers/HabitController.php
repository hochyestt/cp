<?php
namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HabitController extends Controller
{
    public function index(): JsonResponse
    {
        $habits = auth()->user()->habits;
        return response()->json($habits);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:45',
            'progress' => 'nullable|string|max:45',
        ]);

        $habit = auth()->user()->habits()->create($request->only([
            'name', 'progress'
        ]));

        return redirect()->route('home');
    }

    public function show($id): JsonResponse
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        return response()->json($habit);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $habit = auth()->user()->habits()->findOrFail($id);

        $request->validate([
            'name' => 'string|max:45',
            'progress' => 'string|max:45',
        ]);

        $habit->update($request->all());

        return response()->json($habit);
    }

    public function destroy($id): JsonResponse
    {
        $habit = auth()->user()->habits()->findOrFail($id);
        $habit->delete();

        return response()->json(null, 204);
    }
}