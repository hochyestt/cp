<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'telegram_id',
    ];

    // Отношение: каждый TelegramUser принадлежит одному User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}