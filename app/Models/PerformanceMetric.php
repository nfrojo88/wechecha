<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PerformanceMetric extends Model
{
    protected $fillable = [
        'employee_id',
        'period_start',
        'period_end',
        'metric_name',
        'metric_value',
        'target_value',
        'weight',
        'notes',
    ];

    protected $casts = [
        'period_start' => 'date',
        'period_end' => 'date',
        'metric_value' => 'decimal:2',
        'target_value' => 'decimal:2',
        'weight' => 'decimal:2',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function getPercentageAttribute()
    {
        if ($this->target_value == 0) return 0;
        return ($this->metric_value / $this->target_value) * 100;
    }

    public function getStatusAttribute()
    {
        $percentage = $this->percentage;
        if ($percentage >= 100) return 'excellent';
        if ($percentage >= 80) return 'good';
        if ($percentage >= 60) return 'satisfactory';
        return 'needs_improvement';
    }
}
