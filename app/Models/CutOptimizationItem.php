<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CutOptimizationItem extends Model
{
    protected $guarded = [];

    public function cutOptimization() { return $this->belongsTo(CutOptimization::class); }
    public function product() { return $this->belongsTo(Product::class); }
}
