<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakeoffSection extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function sheet()
    {
        return $this->belongsTo(TakeoffSheet::class, 'takeoff_sheet_id');
    }

    public function task()
    {
        return $this->belongsTo(ScheduleTask::class, 'schedule_task_id');
    }

    public function boq()
    {
        return $this->belongsTo(Boq::class, 'boq_id');
    }

    public function items()
    {
        return $this->hasMany(TakeoffItem::class, 'takeoff_section_id')->orderBy('sort_order');
    }
}
