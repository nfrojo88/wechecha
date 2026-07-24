<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAchievement extends Model
{
    protected $fillable = [
        'employee_id',
        'achievement_type',
        'title',
        'description',
        'achievement_date',
        'issuing_authority',
        'award_amount',
    ];

    protected $casts = [
        'achievement_date' => 'date',
        'award_amount' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getTypeIconAttribute()
    {
        return match($this->achievement_type) {
            'Award' => 'fas fa-trophy text-warning',
            'Certification' => 'fas fa-certificate text-info',
            'Project Completion' => 'fas fa-check-circle text-success',
            'Training' => 'fas fa-graduation-cap text-primary',
            default => 'fas fa-star text-secondary'
        };
    }
}
