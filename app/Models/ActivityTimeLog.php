<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityTimeLog extends Model
{
    protected $guarded = [];
    protected $casts = [
        'entered_at' => 'datetime',
        'exited_at' => 'datetime',
    ];

    public function user() { return $this->belongsTo(User::class); }
}
