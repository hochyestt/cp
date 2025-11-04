<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
   
    public function index()
    {
        return response()->json(Auth::user());
    }

   
    public function saveTelegramId(Request $request)
{
    $request->validate([
        'telegram_id' => 'nullable|string|max:50',
    ]);

    $user = $request->user();

    if (!$user) {
        return response()->json(['error' => 'Пользователь не найден'], 404);
    }

    $existing = \App\Models\User::where('telegram_id', $request->telegram_id)
        ->where('id', '!=', $user->id)
        ->first();

    if ($existing) {
        return response()->json([
            'error' => 'Этот Telegram ID уже привязан к другому аккаунту 😅',
        ], 409);
    }

    if ($user->telegram_id === $request->telegram_id) {
        return response()->json(['message' => 'Telegram ID уже сохранён ✅']);
    }

    $user->telegram_id = $request->telegram_id;
    $user->save();

    return response()->json(['message' => 'Telegram ID успешно сохранён ✅']);
}
  
    public function create() {}
    public function store(Request $request) {}
    public function show(string $id) {}
    public function edit(string $id) {}
    public function update(Request $request, string $id) {}
    public function destroy(string $id) {}
}
