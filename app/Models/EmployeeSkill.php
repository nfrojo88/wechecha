<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSkill extends Model
{
    protected $fillable = [
        'employee_id',
        'skill_name',
        'proficiency',
        'years_of_experience',
        'last_updated',
    ];

    protected $casts = [
        'last_updated' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getProficiencyBadge()
    {
        return match($this->proficiency) {
            'beginner' => 'warning',
            'intermediate' => 'info',
            'expert' => 'success',
            default => 'secondary'
        };
    }
}
