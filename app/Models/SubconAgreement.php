<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubconAgreement extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    // Relationships
    public function project() { return $this->belongsTo(Project::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function items() { return $this->hasMany(SubconAgreementItem::class, 'agreement_id'); }
    public function ipcs() { return $this->hasMany(IpcRecord::class, 'agreement_id'); }
    
    // Takeoff Integration
    public function takeoffItems() 
    { 
        return $this->belongsToMany(
            TakeoffItem::class,
            'subcon_agreement_takeoff_items',
            'agreement_id',
            'takeoff_item_id'
        )->withPivot('selected_quantity', 'rate', 'total_amount')->withTimestamps();
    }

    public function takeoffSheet() 
    { 
        return $this->belongsTo(TakeoffSheet::class);
    }

    // Accessors
    public function getTotalAmountAttribute()
    {
        return $this->items()->sum('total_amount') ?? 0;
    }

    public function getTotalTakeoffAmountAttribute()
    {
        return $this->takeoffItems()->sum('subcon_agreement_takeoff_items.total_amount') ?? 0;
    }

    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'draft' => 'secondary',
            'pending' => 'warning',
            'approved' => 'success',
            'active' => 'info',
            'completed' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function isActive()
    {
        return $this->status === 'active' && $this->end_date >= now()->toDateString();
    }

    public function isExpired()
    {
        return $this->end_date < now()->toDateString();
    }
}
