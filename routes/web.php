<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HabitController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TelegramWebhookController; 
use Inertia\Inertia;

// Главная страница (публичная)
Route::get('/', function () {
    return Inertia::render('Home', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// Домашняя страница (после авторизации)
Route::get('/home', function () {
    return Inertia::render('Home', [
        'tasks' => auth()->user()->tasks ?? [],
        'habits' => auth()->user()->habits ?? [],
    ]);
})->middleware(['auth', 'verified'])->name('home');

// Dashboard
Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// История
Route::get('/history', function () {
    return Inertia::render('History');
})->middleware(['auth', 'verified'])->name('history');

// Группа маршрутов требующих аутентификации
Route::middleware(['auth', 'verified'])->group(function () {
    // Профиль
    Route::get('/Profile', function () {
    return Inertia::render('Profile');
})->middleware(['auth', 'verified'])->name('history');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::post('/webhook/telegram', [TelegramWebhookController::class, 'handle'])
    ->name('telegram.webhook');
    // Задачи
    Route::get('/tasks', [TaskController::class, 'index'])->name('tasks.index');         
    Route::post('/tasks', [TaskController::class, 'store'])->name('tasks.store');        
    Route::get('/tasks/{id}', [TaskController::class, 'show'])->name('tasks.show');     
    Route::put('/tasks/{id}', [TaskController::class, 'update'])->name('tasks.update');   
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy'])->name('tasks.destroy'); 

    // Привычки
    Route::get('/habits', [HabitController::class, 'index'])->name('habits.index');       
    Route::post('/habits', [HabitController::class, 'store'])->name('habits.store');      
    Route::get('/habits/{id}', [HabitController::class, 'show'])->name('habits.show');    
    Route::put('/habits/{id}', [HabitController::class, 'update'])->name('habits.update');  
    Route::delete('/habits/{id}', [HabitController::class, 'destroy'])->name('habits.destroy'); 
});

require __DIR__.'/auth.php';