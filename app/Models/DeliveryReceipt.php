<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryReceipt extends Model
{
    protected $fillable = [
        'dr_no', 'purchase_order_id', 'received_by', 'store_id',
        'received_date', 'notes', 'challan_no', 'vehicle_no', 'status',
    ];

    protected $casts = ['received_date' => 'date'];

    public function purchaseOrder() { return $this->belongsTo(PurchaseOrder::class); }
    public function store()         { return $this->belongsTo(Store::class); }
    public function receivedBy()    { return $this->belongsTo(User::class, 'received_by'); }

    public function items()
    {
        return $this->hasMany(DeliveryReceiptItem::class);
    }
}
