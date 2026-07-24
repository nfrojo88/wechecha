<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DailyReportItem extends Model
{
    protected $guarded = [];

    public function dailyReport() { return $this->belongsTo(DailyReport::class, 'daily_report_id'); }
    public function scheduleTask() { return $this->belongsTo(ScheduleTask::class, 'schedule_task_id'); }
}
