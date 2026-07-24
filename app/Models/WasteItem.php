<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class WasteItem extends Model
{
    protected $guarded = [];

    public function waste() { return $this->belongsTo(Waste::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
