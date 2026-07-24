<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseRequest extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'pr_no', 'project_id', 'store_id', 'requested_by', 'material_request_id',
        'priority', 'type', 'required_date', 'justification', 'status',
        'merged_into_pr_id', 'approved_by', 'approved_at', 'rejection_reason',
    ];

    protected $casts = [
        'required_date' => 'date',
        'approved_at'   => 'datetime',
    ];

    public function project()       { return $this->belongsTo(Project::class); }
    public function store()         { return $this->belongsTo(Store::class); }
    public function requestedBy()   { return $this->belongsTo(User::class, 'requested_by'); }
    public function approvedBy()    { return $this->belongsTo(User::class, 'approved_by'); }
    public function materialRequest(){ return $this->belongsTo(MaterialRequest::class); }
    public function mergedInto()    { return $this->belongsTo(PurchaseRequest::class, 'merged_into_pr_id'); }

    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    public function marketResearch()
    {
        return $this->hasMany(MarketResearch::class);
    }

    public function proformaInvoices()
    {
        return $this->hasMany(ProformaInvoice::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }
}
