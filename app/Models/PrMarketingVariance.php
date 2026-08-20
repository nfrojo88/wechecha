<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrMarketingVariance extends Model
{
    protected $table = 'pr_marketing_variances';

    protected $fillable = [
        'purchase_request_id', 'market_price', 'variance_amount',
        'variance_percentage', 'variance_notes', 'added_by',
    ];

    protected $casts = [
        'market_price'        => 'decimal:2',
        'variance_amount'     => 'decimal:2',
        'variance_percentage' => 'decimal:2',
    ];

    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function addedBy()         { return $this->belongsTo(User::class, 'added_by'); }
}
