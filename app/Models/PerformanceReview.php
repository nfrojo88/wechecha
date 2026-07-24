<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PerformanceReview extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'employee_id',
        'reviewer_id',
        'review_period',
        'overall_score',
        'technical_skills_score',
        'soft_skills_score',
        'attendance_score',
        'productivity_score',
        'communication_score',
        'teamwork_score',
        'comments',
        'strengths',
        'areas_for_improvement',
        'development_plan',
        'status',
        'reviewed_at',
    ];

    protected $casts = [
        'review_period' => 'date',
        'overall_score' => 'decimal:2',
        'technical_skills_score' => 'decimal:2',
        'soft_skills_score' => 'decimal:2',
        'attendance_score' => 'decimal:2',
        'productivity_score' => 'decimal:2',
        'communication_score' => 'decimal:2',
        'teamwork_score' => 'decimal:2',
        'reviewed_at' => 'datetime',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    public function getOverallRatingAttribute()
    {
        if ($this->overall_score >= 4.5) return 'Excellent';
        if ($this->overall_score >= 3.5) return 'Good';
        if ($this->overall_score >= 2.5) return 'Satisfactory';
        if ($this->overall_score >= 1.5) return 'Needs Improvement';
        return 'Unsatisfactory';
    }

    public function getRatingBadgeAttribute()
    {
        if ($this->overall_score >= 4.5) return 'success';
        if ($this->overall_score >= 3.5) return 'info';
        if ($this->overall_score >= 2.5) return 'warning';
        return 'danger';
    }
}
