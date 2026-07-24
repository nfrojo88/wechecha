<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErpPlanTask extends Model {
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'metadata' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
    ];
    public function header() { return $this->belongsTo(ErpPlanHeader::class, 'plan_header_id'); }
    public function parent() { return $this->belongsTo(ErpPlanTask::class, 'parent_task_id'); }
    public function children() { return $this->hasMany(ErpPlanTask::class, 'parent_task_id'); }
    public function dependencies() { return $this->hasMany(ErpPlanTaskDependency::class, 'task_id'); }
    public function resources() { return $this->hasMany(ErpPlanTaskResource::class, 'task_id'); }
    public function assignee() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function store() { return $this->belongsTo(Store::class, 'store_id'); }
}
