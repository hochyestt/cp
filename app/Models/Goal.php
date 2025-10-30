<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gpal extends Model
{
    use HasFactory;

    protected $table = 'goal';

    protected $fillable = [
        'name',
        'progress',
        'habit_id',
        'habit_user_id',
        'task_id',
        'task_user_id'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function habit()
    {
        return $this->belongsTo(Habit::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function habitUser()
    {
        return $this->belongsTo(User::class, 'habit_user_id');
    }

    public function taskUser()
    {
        return $this->belongsTo(User::class, 'task_user_id');
    }
}