<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketResearch extends Model
{
    protected $fillable = [
        'purchase_request_id', 'supplier_id', 'quoted_total', 'status', 'notes', 'created_by',
    ];

    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function supplier()        { return $this->belongsTo(Supplier::class); }
    public function createdBy()       { return $this->belongsTo(User::class, 'created_by'); }

    public function items()
    {
        return $this->hasMany(MarketResearchItem::class);
    }
}
