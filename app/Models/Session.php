<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;

    // Указываем таблицу, если она отличается от стандартного имени модели (sessions)
    protected $table = 'sessions';

    protected $fillable = [
        'id',
        'user_id',
        'ip_address',
        'user_agent',
        'payload',
        'last_activity',
    ];

    // Связь с пользователем
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
