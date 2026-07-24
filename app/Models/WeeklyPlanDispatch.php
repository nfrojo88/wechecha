<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyPlanDispatch extends Model
{
    protected $guarded = [];
    protected $casts = ['week_start' => 'date', 'week_end' => 'date'];

    public function project() { return $this->belongsTo(Project::class); }
    public function dispatchedTo() { return $this->belongsTo(User::class, 'dispatched_to'); }
    public function tasks() { return $this->hasMany(WeeklyPlanDispatchTask::class, 'dispatch_id'); }
}
