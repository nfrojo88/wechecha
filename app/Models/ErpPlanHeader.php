<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ErpPlanHeader extends Model {
    use HasFactory, SoftDeletes;
    protected $guarded = ['id'];
    protected $casts = [
        'plan_start_date' => 'date',
        'plan_end_date' => 'date',
        'approved_at' => 'datetime',
    ];
    public function project() { return $this->belongsTo(Project::class); }
    public function schedule() { return $this->belongsTo(\App\Models\Schedule::class, 'schedule_id'); }
    public function tasks() { return $this->hasMany(ErpPlanTask::class, 'plan_header_id'); }
    public function baselines() { return $this->hasMany(PlanBaseline::class, 'plan_header_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function approver() { return $this->belongsTo(User::class, 'approved_by'); }
}
