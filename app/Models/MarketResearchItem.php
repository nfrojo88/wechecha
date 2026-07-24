<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MarketResearchItem extends Model
{
    protected $fillable = [
        'market_research_id', 'product_id', 'unit_price', 'quantity', 'delivery_days', 'remarks',
    ];

    public function marketResearch() { return $this->belongsTo(MarketResearch::class); }
    public function product()        { return $this->belongsTo(Product::class); }
}
