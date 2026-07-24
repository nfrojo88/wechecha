<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name', 'code', 'contact_person', 'phone', 'email',
        'address', 'tax_id', 'status', 'rating', 'notes', 'bank_details',
    ];

    protected $casts = [
        'bank_details' => 'array',
        'rating'       => 'decimal:2',
    ];

    public function purchaseRequests()
    {
        return $this->hasMany(PurchaseRequest::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function proformaInvoices()
    {
        return $this->hasMany(ProformaInvoice::class);
    }

    public function marketResearch()
    {
        return $this->hasMany(MarketResearch::class);
    }
}
