<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class MaterialPlan extends Model
{
    protected $guarded = [];

    public function project() { return $this->belongsTo(Project::class); }
    public function planHeader() { return $this->belongsTo(ErpPlanHeader::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(MaterialPlanItem::class); }
}
