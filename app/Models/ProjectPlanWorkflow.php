<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectPlanWorkflow extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        'plan_type',
        'status',
        'submitted_at',
        // Step 1 — Planning Manager
        'planning_manager_id',
        'planning_manager_at',
        'planning_manager_note',
        // Step 2 — Coordinator
        'coordinator_id',
        'coordinator_at',
        'coordinator_note',
        // Step 3 — Technical Manager
        'tech_manager_id',
        'tech_manager_at',
        'tech_manager_note',
        // Step 4 — GM
        'gm_id',
        'gm_at',
        'gm_note',
        'budget_allocated',
        // Rejection
        'rejected_by',
        'rejected_at',
        'rejection_reason',
        'rejected_at_step',
        'created_by',
    ];

    protected $casts = [
        'submitted_at'           => 'datetime',
        'planning_manager_at'    => 'datetime',
        'coordinator_at'         => 'datetime',
        'tech_manager_at'        => 'datetime',
        'gm_at'                  => 'datetime',
        'rejected_at'            => 'datetime',
        'budget_allocated'       => 'decimal:2',
    ];

    // ── Ordered steps ─────────────────────────────────────────────────────
    public const STEPS = [
        'submitted'                    => 'Submitted for Review',
        'planning_manager_approved'    => 'Planning Manager Review',
        'coordinator_approved'         => 'Coordinator Review',
        'technical_manager_approved'   => 'Technical Manager Review',
        'gm_approved'                  => 'GM Approval & Budget Allocation',
    ];

    public const STATUS_LABELS = [
        'draft'                        => 'Draft',
        'submitted'                    => 'Submitted',
        'planning_manager_approved'    => 'Planning Manager Approved',
        'coordinator_approved'         => 'Coordinator Approved',
        'technical_manager_approved'   => 'Technical Manager Approved',
        'gm_approved'                  => 'Fully Approved',
        'rejected'                     => 'Rejected',
    ];

    // ── Relationships ─────────────────────────────────────────────────────

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function planningManager()
    {
        return $this->belongsTo(User::class, 'planning_manager_id');
    }

    public function coordinator()
    {
        return $this->belongsTo(User::class, 'coordinator_id');
    }

    public function techManager()
    {
        return $this->belongsTo(User::class, 'tech_manager_id');
    }

    public function gm()
    {
        return $this->belongsTo(User::class, 'gm_id');
    }

    public function rejector()
    {
        return $this->belongsTo(User::class, 'rejected_by');
    }

    public function budgetAllocations()
    {
        return $this->hasMany(ProjectBudgetAllocation::class, 'workflow_id');
    }

    // ── State helpers ─────────────────────────────────────────────────────

    public function isFullyApproved(): bool
    {
        return $this->status === 'gm_approved';
    }

    public function isRejected(): bool
    {
        return $this->status === 'rejected';
    }

    public function isActive(): bool
    {
        return !in_array($this->status, ['gm_approved', 'rejected']);
    }

    /**
     * Returns which role must act next.
     */
    public function nextRequiredRole(): ?string
    {
        return match ($this->status) {
            'submitted'                  => 'planning_manager',
            'planning_manager_approved'  => 'coordinator',
            'coordinator_approved'       => 'technical_manager',
            'technical_manager_approved' => 'gm',
            default                      => null,
        };
    }

    /**
     * Human-readable next step label.
     */
    public function nextStepLabel(): string
    {
        return match ($this->status) {
            'draft'                        => 'Awaiting submission by Planning Team',
            'submitted'                    => 'Awaiting Planning Manager review',
            'planning_manager_approved'    => 'Awaiting Coordinator review',
            'coordinator_approved'         => 'Awaiting Technical Manager review',
            'technical_manager_approved'   => 'Awaiting GM approval & budget allocation',
            'gm_approved'                  => 'Fully approved — budget allocated',
            'rejected'                     => 'Rejected at step: ' . ($this->rejected_at_step ?? 'unknown'),
            default                        => 'Unknown',
        };
    }

    /**
     * Progress percentage for UI progress bar (0–100).
     */
    public function progressPercent(): int
    {
        return match ($this->status) {
            'draft'                        => 0,
            'submitted'                    => 20,
            'planning_manager_approved'    => 40,
            'coordinator_approved'         => 60,
            'technical_manager_approved'   => 80,
            'gm_approved'                  => 100,
            'rejected'                     => 0,
            default                        => 0,
        };
    }
}
