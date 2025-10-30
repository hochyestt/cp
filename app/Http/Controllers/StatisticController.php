<?php

namespace App\Http\Controllers;

use App\Models\Statistic;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StatisticController extends Controller
{
    public function index(): JsonResponse
    {
        $statistics = Statistic::where('user_id', auth()->id())->get();
        return response()->json($statistics);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'complet_tasks' => 'required|integer',
            
            'complet_habit' => 'required|integer',
            'report' => 'required|date',
        ]);

        $statistic = Statistic::create([
            ...$request->all(),
            'user_id' => auth()->id()
        ]);

        return response()->json($statistic, 201);
    }

    public function show(Statistic $statistic): JsonResponse
    {
        $this->authorize('view', $statistic);
        return response()->json($statistic);
    }

    public function update(Request $request, Statistic $statistic): JsonResponse
    {
        $this->authorize('update', $statistic);

        $request->validate([
            'complet_tasks' => 'integer',
            'complet_habit' => 'integer',
            'report' => 'date',
        ]);

        $statistic->update($request->all());

        return response()->json($statistic);
    }

    public function destroy(Statistic $statistic): JsonResponse
    {
        $this->authorize('delete', $statistic);
        $statistic->delete();

        return response()->json(null, 204);
    }
}