<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'letter_id',
        'message',
        'is_read',
        'read_at',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function letter()
    {
        return $this->belongsTo(Letter::class, 'letter_id');
    }
}
