<?php

namespace App\Services;

use App\Exceptions\BudgetExceededException;
use App\Models\Project;

/**
 * BudgetGuardService
 *
 * Enforces three-tier budget thresholds:
 *   0% – 80%   → "safe"    — normal processing
 *   >80% – 100% → "at_risk" — flagged, but allowed
 *   ≥100%       → "blocked" — hard stop; throws BudgetExceededException
 *
 * Only projects with a GM-approved plan and a budget allocation are guarded.
 * Projects without a budget are allowed through (unguarded).
 */
class BudgetGuardService
{
    public const THRESHOLD_AT_RISK = 80.0;
    public const THRESHOLD_BLOCKED = 100.0;

    /**
     * Check whether a proposed amount can be processed against a project's budget.
     *
     * Returns an array:
     *   'status'      => 'safe' | 'at_risk' | 'blocked'
     *   'utilization' => float (current % BEFORE the new amount)
     *   'utilization_after' => float (% AFTER the new amount)
     *   'message'     => string
     *   'guarded'     => bool (false if project has no budget — skip all checks)
     *
     * @throws BudgetExceededException if status would be 'blocked'
     */
    public function check(Project $project, float $amount, bool $throwOnBlocked = false): array
    {
        // If no budget allocated, allow freely
        if (!$project->hasBudgetAllocated()) {
            return [
                'status'            => 'safe',
                'utilization'       => 0.0,
                'utilization_after' => 0.0,
                'message'           => 'No budget allocated — unguarded.',
                'guarded'           => false,
            ];
        }

        $allocated       = (float) $project->budget_allocated;
        $consumed        = (float) $project->budget_consumed;
        $consumedAfter   = $consumed + $amount;

        $utilizationPct      = $allocated > 0 ? round(($consumed / $allocated) * 100, 2) : 0.0;
        $utilizationAfterPct = $allocated > 0 ? round(($consumedAfter / $allocated) * 100, 2) : 0.0;

        // Hard stop: even before the new amount the budget is already at/over 100%
        if ($utilizationPct >= self::THRESHOLD_BLOCKED) {
            if ($throwOnBlocked) {
                throw new BudgetExceededException($utilizationPct, $allocated, $consumed);
            }
            return [
                'status'            => 'blocked',
                'utilization'       => $utilizationPct,
                'utilization_after' => $utilizationPct,
                'message'           => sprintf(
                    'Budget is fully consumed (%.1f%%). The GM must allocate additional budget.',
                    $utilizationPct
                ),
                'guarded'           => true,
            ];
        }

        // Hard stop: new amount would push it to/over 100%
        if ($utilizationAfterPct >= self::THRESHOLD_BLOCKED) {
            if ($throwOnBlocked) {
                throw new BudgetExceededException($utilizationAfterPct, $allocated, $consumedAfter);
            }
            return [
                'status'            => 'blocked',
                'utilization'       => $utilizationPct,
                'utilization_after' => $utilizationAfterPct,
                'message'           => sprintf(
                    'This expense (%.2f ETB) would exceed the project budget (%.1f%% → %.1f%%). The GM must allocate additional budget before proceeding.',
                    $amount,
                    $utilizationPct,
                    $utilizationAfterPct
                ),
                'guarded'           => true,
            ];
        }

        // At-risk zone
        if ($utilizationAfterPct > self::THRESHOLD_AT_RISK) {
            return [
                'status'            => 'at_risk',
                'utilization'       => $utilizationPct,
                'utilization_after' => $utilizationAfterPct,
                'message'           => sprintf(
                    'Warning: Budget utilization will reach %.1f%% after this expense. You are in the At-Risk zone (>80%%).',
                    $utilizationAfterPct
                ),
                'guarded'           => true,
            ];
        }

        return [
            'status'            => 'safe',
            'utilization'       => $utilizationPct,
            'utilization_after' => $utilizationAfterPct,
            'message'           => sprintf('Budget utilization: %.1f%%', $utilizationAfterPct),
            'guarded'           => true,
        ];
    }

    /**
     * Atomically increment budget_consumed on the project.
     * Call this AFTER a successful expense/PO is saved.
     *
     * @throws BudgetExceededException if the result would exceed budget
     */
    public function consume(Project $project, float $amount): void
    {
        // Re-check with throw enabled
        $this->check($project, $amount, throwOnBlocked: true);

        // Atomic increment to avoid race conditions
        $project->increment('budget_consumed', $amount);
        $project->refresh();
    }

    /**
     * Decrement budget_consumed (called on expense rejection/deletion).
     */
    public function release(Project $project, float $amount): void
    {
        $project->decrement('budget_consumed', min($amount, (float) $project->budget_consumed));
    }
}
