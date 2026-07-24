<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpPlanTaskResource extends Model {
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = ['details' => 'array'];
    public function task() { return $this->belongsTo(ErpPlanTask::class, 'task_id'); }
}
