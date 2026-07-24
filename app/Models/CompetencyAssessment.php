<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompetencyAssessment extends Model
{
    protected $fillable = [
        'employee_id',
        'competency_id',
        'current_level',
        'target_level',
        'assessed_date',
        'assessed_by',
        'notes',
    ];

    protected $casts = [
        'assessed_date' => 'date',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function competency()
    {
        return $this->belongsTo(Competency::class);
    }

    public function assessedByUser()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }

    public function getGapAttribute()
    {
        return $this->target_level - $this->current_level;
    }

    public function getLevelBadgeAttribute()
    {
        return match($this->current_level) {
            1 => 'danger',
            2 => 'warning',
            3 => 'info',
            4 => 'success',
            5 => 'success',
            default => 'secondary'
        };
    }
}
