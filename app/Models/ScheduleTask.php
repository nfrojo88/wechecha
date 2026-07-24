<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScheduleTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'schedule_id', 'parent_task_id', 'wbs_code', 'name', 'type',
        'priority', 'status', 'is_milestone', 'predecessor_id',
        'start_date', 'end_date', 'duration_days', 'planned_cost'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_milestone' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($task) {
            // Update current parent
            if ($task->parent_task_id) {
                $parent = $task->parent;
                if ($parent) {
                    $parent->recalculateDates();
                }
            }
            
            // If parent changed, update the old parent too
            if ($task->wasChanged('parent_task_id')) {
                $oldParentId = $task->getOriginal('parent_task_id');
                if ($oldParentId) {
                    $oldParent = self::find($oldParentId);
                    if ($oldParent) {
                        $oldParent->recalculateDates();
                    }
                }
            }
        });

        static::deleted(function ($task) {
            if ($task->parent_task_id) {
                $parent = self::find($task->parent_task_id);
                if ($parent) {
                    $parent->recalculateDates();
                }
            }
        });
    }

    public function recalculateDates()
    {
        // Get the earliest start date and latest end date from children
        $minStartDate = $this->children()->min('start_date');
        $maxEndDate = $this->children()->max('end_date');
        
        $this->start_date = $minStartDate;
        $this->end_date = $maxEndDate;
        
        if ($this->isDirty(['start_date', 'end_date'])) {
            $this->save(); // This will trigger the saved event and bubble up
        }
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function parent()
    {
        return $this->belongsTo(ScheduleTask::class, 'parent_task_id');
    }

    public function children()
    {
        return $this->hasMany(ScheduleTask::class, 'parent_task_id');
    }

    public function predecessor()
    {
        return $this->belongsTo(ScheduleTask::class, 'predecessor_id');
    }
}
