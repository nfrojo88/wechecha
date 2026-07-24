<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class EngWorkOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'eng_work_orders';

    protected $guarded = ['id'];

    protected $casts = [
        'start_datetime'      => 'datetime',
        'end_datetime'        => 'datetime',
        'recurrence_end_date' => 'date',
    ];

    // Status constants
    const STATUS_DRAFT       = 'draft';
    const STATUS_ASSIGNED    = 'assigned';
    const STATUS_ACCEPTED    = 'accepted';
    const STATUS_DECLINED    = 'declined';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_ON_HOLD     = 'on_hold';
    const STATUS_COMPLETED   = 'completed';
    const STATUS_CANCELLED   = 'cancelled';

    const PRIORITY_LOW    = 'low';
    const PRIORITY_MEDIUM = 'medium';
    const PRIORITY_HIGH   = 'high';
    const PRIORITY_URGENT = 'urgent';

    // ── Relationships ──────────────────────────────────────────────────────────

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /** The engineers assigned to this work order (via pivot) */
    public function engineers()
    {
        return $this->belongsToMany(User::class, 'eng_work_order_assignees', 'work_order_id', 'user_id')
                    ->withPivot(['status', 'decline_reason', 'actual_hours', 'accepted_at', 'completed_at'])
                    ->withTimestamps();
    }

    public function assignees()
    {
        return $this->hasMany(EngWorkOrderAssignee::class, 'work_order_id');
    }

    public function comments()
    {
        return $this->hasMany(EngWorkOrderComment::class, 'work_order_id')->latest();
    }

    public function statusHistory()
    {
        return $this->hasMany(EngWorkOrderStatusHistory::class, 'work_order_id')->latest();
    }

    // ── Helpers ────────────────────────────────────────────────────────────────

    public function isAssignedTo(int $userId): bool
    {
        return $this->engineers()->where('users.id', $userId)->exists();
    }

    public function durationHours(): float
    {
        return round($this->start_datetime->diffInMinutes($this->end_datetime) / 60, 1);
    }

    public function priorityColor(): string
    {
        return match ($this->priority) {
            'urgent' => '#ef4444',
            'high'   => '#f97316',
            'medium' => '#3b82f6',
            default  => '#6b7280',
        };
    }

    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            'completed'   => 'badge bg-success',
            'in_progress' => 'badge bg-primary',
            'accepted'    => 'badge bg-info',
            'declined'    => 'badge bg-danger',
            'on_hold'     => 'badge bg-warning text-dark',
            'cancelled'   => 'badge bg-secondary',
            'assigned'    => 'badge bg-purple',
            default       => 'badge bg-light text-dark',
        };
    }

    // ── Scopes ─────────────────────────────────────────────────────────────────

    public function scopeForEngineer($query, int $userId)
    {
        return $query->whereHas('engineers', fn($q) => $q->where('users.id', $userId));
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_datetime', '>', now())->where('status', '!=', 'cancelled');
    }

    public function scopeOverdue($query)
    {
        return $query->where('end_datetime', '<', now())
                     ->whereNotIn('status', ['completed', 'cancelled']);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('start_datetime', today());
    }
}
