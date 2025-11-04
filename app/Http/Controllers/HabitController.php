<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class HabitController extends Controller
{

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:45',
            'progress' => 'nullable|string|max:45',
            'frequency_type' => 'required|in:day,week',
            'frequency_value' => 'required|integer|min:1',
        ]);
        
        $now = now();

        $habit = auth()->user()->habits()->create(array_merge(
            $request->only(['name', 'progress', 'frequency_type', 'frequency_value']),
            [
                'next_notification' => $now, 
                'counter_reset_at' => $now,
            ]
        ));

        return redirect()->route('home');
    }
public function destroy(Habit $habit)
    {
        if ($habit->user_id !== auth()->id()) {
            abort(403, 'У вас нет доступа к этой привычке.');
        }

        $habit->delete();

        return redirect()->back(); 
    }
    public function markDone(Habit $habit): JsonResponse
    {
        $habit->times_done_since_reset++;
        
        $nextNotification = $habit->calculateNextNotificationTime();

        $habit->update([
            'last_done_at' => now(),
            'next_notification' => $nextNotification, 
            'times_done_since_reset' => $habit->times_done_since_reset,
        ]);

        return response()->json([
            'message' => 'Привычка выполнена ✅', 
            'next_notification' => $nextNotification->format('Y-m-d H:i:s')
        ]);
    }
}