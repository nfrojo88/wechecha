<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrGmDecision extends Model
{
    protected $fillable = [
        'purchase_request_id', 'round', 'decision',
        'payment_method', 'notes', 'decided_by', 'decided_at',
    ];

    protected $casts = ['decided_at' => 'datetime'];

    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function decidedBy()       { return $this->belongsTo(User::class, 'decided_by'); }
}
