<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    use HasFactory;

    // Поля, которые можно массово заполнять (mass assignable)
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'status', // Добавлено из вашего кода
        'priority', // Добавлено из вашего кода
        'start_time',
        'end_time',
        'yandex_event_id', // Добавлено из вашего кода
        'notified', // Это поле необходимо для бота
        'completed', // Это поле необходимо для бота
    ];

    // Определение преобразования типов данных для колонок базы данных
    // Это гарантирует, что 'start_time' и 'end_time' будут объектами Carbon,
    // а 'notified' и 'completed' будут булевыми значениями.
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'notified' => 'boolean',
        'completed' => 'boolean',
    ];

    /**
     * Получить пользователя, которому принадлежит задача.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}