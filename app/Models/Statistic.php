<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Statistic extends Model
{
    use HasFactory;

    protected $primaryKey = 'stat_id';

    protected $fillable = [
        'user_id',
        'complet_tasks',
        'complet_habit',
        'report'
    ];

    protected $casts = [
        'report' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}