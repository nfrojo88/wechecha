<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PrWorkflowLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'purchase_request_id', 'from_stage', 'to_stage',
        'action', 'actor_role', 'notes', 'actor_id', 'created_at',
    ];

    protected $casts = ['created_at' => 'datetime'];

    public function purchaseRequest() { return $this->belongsTo(PurchaseRequest::class); }
    public function actor()           { return $this->belongsTo(User::class, 'actor_id'); }
}
