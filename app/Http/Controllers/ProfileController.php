<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        return Inertia::render('Profile', [
            'user' => $request->user(),
        ]);
    }

    
    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'  => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        $request->user()->update($validated);

        return Redirect::route('profile.edit')->with('success', 'Профиль обновлён ✅');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

   
    public function saveTelegramId(Request $request)
    {
        $validated = $request->validate([
            'telegram_id' => ['nullable', 'string', 'max:50'],
        ]);

        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Пользователь не найден'], 404);
        }

        $user->update([
            'telegram_id' => $validated['telegram_id'],
        ]);

        return response()->json(['message' => 'Telegram ID сохранён ✅']);
    }
}
