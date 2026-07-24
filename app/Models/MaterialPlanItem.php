<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaterialPlanItem extends Model
{
    protected $guarded = [];

    public function materialPlan() { return $this->belongsTo(MaterialPlan::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function task() { return $this->belongsTo(ErpPlanTask::class); }
    public function store() { return $this->belongsTo(Store::class); }
    public function generatedPr() { return $this->belongsTo(PurchaseRequest::class, 'generated_pr_id'); }
}
