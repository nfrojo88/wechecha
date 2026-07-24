<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SupportTicket extends Model
{
    protected $fillable = [
        'ticket_no', 'user_id', 'category', 'subject', 'description',
        'priority', 'status', 'assigned_to', 'resolved_at',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($ticket) {
            $last = static::orderBy('id', 'desc')->first();
            $next = $last ? ($last->id + 1) : 1;
            $ticket->ticket_no = 'TKT-' . str_pad($next, 4, '0', STR_PAD_LEFT);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(TicketReply::class, 'ticket_id');
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'open'        => '<span class="badge bg-danger">Open</span>',
            'in_progress' => '<span class="badge bg-warning">In Progress</span>',
            'resolved'    => '<span class="badge bg-success">Resolved</span>',
            'closed'      => '<span class="badge bg-secondary">Closed</span>',
            default       => '<span class="badge bg-secondary">' . ucfirst($this->status) . '</span>',
        };
    }

    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'urgent' => '<span class="badge" style="background:#fee2e2;color:#991b1b;">🔴 Urgent</span>',
            'high'   => '<span class="badge" style="background:#fef3c7;color:#92400e;">🟠 High</span>',
            'medium' => '<span class="badge" style="background:#dbeafe;color:#1e40af;">🔵 Medium</span>',
            'low'    => '<span class="badge bg-secondary">🟢 Low</span>',
            default  => '<span class="badge bg-secondary">' . ucfirst($this->priority) . '</span>',
        };
    }
}
