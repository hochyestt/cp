<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\HabitController;


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/tasks', [TaskController::class, 'index']);         
    Route::post('/tasks', [TaskController::class, 'store']);        
    Route::get('/tasks/{id}', [TaskController::class, 'show']);     
    Route::put('/tasks/{id}', [TaskController::class, 'update']);   
    Route::delete('/tasks/{id}', [TaskController::class, 'destroy']); 

    Route::get('/habits', [HabitController::class, 'index']);       
    Route::post('/habits', [HabitController::class, 'store']);      
    Route::get('/habits/{id}', [HabitController::class, 'show']);    
    Route::put('/habits/{id}', [HabitController::class, 'update']);  
    Route::delete('/habits/{id}', [HabitController::class, 'destroy']); 
});