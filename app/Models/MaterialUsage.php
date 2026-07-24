<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ScopesByStore;

class MaterialUsage extends Model
{
    use ScopesByStore;
    protected $guarded = [];
    protected $casts = ['usage_date' => 'date'];

    public function project() { return $this->belongsTo(Project::class); }
    public function store() { return $this->belongsTo(Store::class); }
    public function task() { return $this->belongsTo(ErpPlanTask::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(MaterialUsageItem::class); }
}
