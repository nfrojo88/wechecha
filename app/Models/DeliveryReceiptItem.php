<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryReceiptItem extends Model
{
    protected $fillable = [
        'delivery_receipt_id', 'product_id', 'po_item_id',
        'quantity_received', 'accepted_quantity', 'rejected_quantity', 'unit', 'rejection_reason',
    ];

    public function deliveryReceipt() { return $this->belongsTo(DeliveryReceipt::class); }
    public function product()         { return $this->belongsTo(Product::class); }
    public function poItem()          { return $this->belongsTo(PurchaseOrderItem::class, 'po_item_id'); }
}
