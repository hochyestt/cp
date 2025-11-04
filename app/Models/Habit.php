<?php
// app/Models/Habit.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'progress',
        'user_id',
        'next_notification',
        'last_done_at',     
        'frequency_type',
        'frequency_value',
        'times_done_since_reset',
        'counter_reset_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'next_notification' => 'datetime',
        'last_done_at' => 'datetime',
        'counter_reset_at' => 'datetime',
    ];

    public const FREQUENCY_DAY = 'day';
    public const FREQUENCY_WEEK = 'week';

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    
    public function isCompletedForCurrentPeriod(): bool
    {
        return $this->times_done_since_reset >= $this->frequency_value;
    }
    
   
    public function calculateNextNotificationTime(): Carbon
    {
        $now = now();
        
        if ($this->frequency_type === self::FREQUENCY_DAY) {
            if ($this->frequency_value > 1 && $this->times_done_since_reset < $this->frequency_value) {
                return $now->addHours(4); // Например, каждые 4 часа до достижения лимита
            }
            return $now->startOfDay()->addDay(); 

        } elseif ($this->frequency_type === self::FREQUENCY_WEEK) {
            if ($this->isCompletedForCurrentPeriod()) {
                return $now->startOfWeek()->addWeek(); 
            }
            return $now->startOfDay()->addDay(); 
        }
        
        return $now->addDay();
    }
    
   
    public function checkAndResetCounter(): void
    {
        $now = now();
        $resetTime = $this->counter_reset_at ?? $now->subMinute(); // Инициализация
        
        if ($this->frequency_type === self::FREQUENCY_DAY && $resetTime->lt($now->startOfDay())) {
            $this->times_done_since_reset = 0;
            $this->counter_reset_at = $now;
            $this->save();
            
        } elseif ($this->frequency_type === self::FREQUENCY_WEEK && $resetTime->lt($now->startOfWeek())) {
            $this->times_done_since_reset = 0;
            $this->counter_reset_at = $now;
            $this->save();
        }
    }
}