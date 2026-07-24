<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpPlanTaskDependency extends Model {
    use HasFactory;
    protected $guarded = ['id'];
    public function task() { return $this->belongsTo(ErpPlanTask::class, 'task_id'); }
    public function dependsOn() { return $this->belongsTo(ErpPlanTask::class, 'depends_on_task_id'); }
}
