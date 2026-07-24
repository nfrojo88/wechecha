<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Schedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'name',
        'status',
        'start_date',
        'end_date',
        'progress',
        'created_by',
        'sent_to_coordinator',
        'sent_at',
        'sent_by',
    ];

    protected $casts = [
        'start_date'           => 'date',
        'end_date'             => 'date',
        'progress'             => 'decimal:2',
        'sent_to_coordinator'  => 'boolean',
        'sent_at'              => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }

    public function erpPlan()
    {
        return $this->hasOne(\App\Models\ErpPlanHeader::class, 'schedule_id');
    }

    public function tasks()
    {
        return $this->hasMany(ScheduleTask::class)->orderBy('wbs_code');
    }

    public function baselines()
    {
        return $this->hasMany(ScheduleBaseline::class)->latest();
    }
}
