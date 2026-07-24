<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectBudgetAllocation extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'workflow_id',
        'amount',
        'allocation_type',
        'reason',
        'allocated_by',
        'allocated_at',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'allocated_at' => 'datetime',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function workflow()
    {
        return $this->belongsTo(ProjectPlanWorkflow::class, 'workflow_id');
    }

    public function allocator()
    {
        return $this->belongsTo(User::class, 'allocated_by');
    }
}
