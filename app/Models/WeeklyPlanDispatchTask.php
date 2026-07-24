<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyPlanDispatchTask extends Model
{
    protected $guarded = [];

    public function dispatch() { return $this->belongsTo(WeeklyPlanDispatch::class, 'dispatch_id'); }
    public function scheduleTask() { return $this->belongsTo(ScheduleTask::class, 'schedule_task_id'); }
}
