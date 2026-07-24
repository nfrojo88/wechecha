<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManpowerRequest extends Model
{
    protected $fillable = [
        'project_id', 'requested_by', 'task_id', 'type',
        'required_date', 'requirements', 'status', 'notes',
    ];

    protected $casts = ['required_date' => 'date'];

    public function project()     { return $this->belongsTo(Project::class); }
    public function requestedBy() { return $this->belongsTo(User::class, 'requested_by'); }
    public function task()        { return $this->belongsTo(ErpPlanTask::class, 'task_id'); }
    public function items()       { return $this->hasMany(ManpowerRequestItem::class); }
}
