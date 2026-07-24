<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaterialUsageItem extends Model
{
    protected $guarded = [];

    public function materialUsage() { return $this->belongsTo(MaterialUsage::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
