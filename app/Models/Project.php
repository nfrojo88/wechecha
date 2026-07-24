<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'description',
        'location',
        'client_name',
        'client_contact',
        'status',
        'start_date',
        'end_date',
        'contract_value',
        'budget_allocated',
        'budget_consumed',
        'default_store_id',
        'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'contract_value' => 'decimal:2',
        'budget_allocated' => 'decimal:2',
        'budget_consumed' => 'decimal:2',
    ];

    public function defaultStore()
    {
        return $this->belongsTo(Store::class, 'default_store_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function budgets()
    {
        return $this->hasMany(ProjectBudget::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function planWorkflows()
    {
        return $this->hasMany(ProjectPlanWorkflow::class);
    }

    public function activeWorkflow()
    {
        return $this->hasOne(ProjectPlanWorkflow::class)->latestOfMany();
    }

    public function budgetAllocations()
    {
        return $this->hasMany(ProjectBudgetAllocation::class);
    }

    // ── Budget helpers ────────────────────────────────────────────────────

    /**
     * Budget utilization as a percentage (0–∞).
     */
    public function budgetUtilizationPercent(): float
    {
        if (!$this->budget_allocated || $this->budget_allocated == 0) {
            return 0.0;
        }
        return round(($this->budget_consumed / $this->budget_allocated) * 100, 2);
    }

    /**
     * Returns 'safe' | 'at_risk' | 'blocked'
     */
    public function budgetStatus(): string
    {
        $pct = $this->budgetUtilizationPercent();
        if ($pct >= 100) return 'blocked';
        if ($pct > 80)   return 'at_risk';
        return 'safe';
    }

    /**
     * Returns remaining budget amount.
     */
    public function budgetRemaining(): float
    {
        return max(0, (float)$this->budget_allocated - (float)$this->budget_consumed);
    }

    /**
     * Whether the GM has formally allocated a budget via the workflow.
     */
    public function hasBudgetAllocated(): bool
    {
        return !is_null($this->budget_allocated) && $this->budget_allocated > 0;
    }

    // ── Auto-status from workflow ─────────────────────────────────────────

    /**
     * Map a planning_phase_status value to the correct project status.
     *
     * Rules:
     *  - gm_approved           → active   (budget allocated, construction can start)
     *  - anything else in the  → planning  (still in planning pipeline)
     *    approval chain
     *
     * Terminal / manual states (on_hold, completed, cancelled, handover,
     * bidding) are NEVER set by this method — they remain under manual control.
     */
    public static function resolveStatusFromPlanningPhase(string $planningPhaseStatus): string
    {
        return match ($planningPhaseStatus) {
            'gm_approved' => 'active',
            default       => 'planning',
        };
    }

    /**
     * Recalculate and persist the project `status` from its current
     * `planning_phase_status`, UNLESS the project is in a terminal /
     * manually-managed state that should not be touched by the workflow.
     *
     * Safe to call after every workflow approval step.
     */
    public function syncStatusFromWorkflow(): void
    {
        // Do not override terminal or manually-managed statuses
        $manualStates = ['on_hold', 'completed', 'cancelled', 'handover', 'bidding'];

        if (in_array($this->status, $manualStates)) {
            return;
        }

        $newStatus = self::resolveStatusFromPlanningPhase(
            $this->planning_phase_status ?? 'draft'
        );

        if ($this->status !== $newStatus) {
            $this->update(['status' => $newStatus]);
        }
    }
}

