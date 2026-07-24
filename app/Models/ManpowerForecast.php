<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ManpowerForecast extends Model
{
    protected $fillable = [
        'project_id',
        'week_starting',
        'designation_id',
        'forecasted_headcount',
        'forecasted_hours',
        'notes',
        'created_by',
        'status',
    ];

    protected $casts = [
        'week_starting' => 'date',
        'forecasted_headcount' => 'decimal:2',
        'forecasted_hours' => 'decimal:2',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function designation()
    {
        return $this->belongsTo(Designation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignments()
    {
        return $this->hasMany(ManpowerAssignment::class);
    }

    public function getAvailableHeadcount()
    {
        return $this->forecasted_headcount - $this->assignments()->count();
    }
}
