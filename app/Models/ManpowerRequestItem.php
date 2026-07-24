<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManpowerRequestItem extends Model
{
    protected $fillable = [
        'manpower_request_id', 'role_title', 'quantity',
        'skill_level', 'daily_rate', 'duration_days', 'notes',
    ];

    public function manpowerRequest() { return $this->belongsTo(ManpowerRequest::class); }
}
