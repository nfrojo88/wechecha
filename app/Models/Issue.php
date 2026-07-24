<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Issue extends Model
{
    use SoftDeletes;
    protected $guarded = [];
    protected $casts = ['due_date' => 'date', 'resolved_at' => 'datetime'];

    public function project() { return $this->belongsTo(Project::class); }
    public function reportedBy() { return $this->belongsTo(User::class, 'reported_by'); }
    public function assignedTo() { return $this->belongsTo(User::class, 'assigned_to'); }
    public function task() { return $this->belongsTo(ErpPlanTask::class); }
    public function comments() { return $this->hasMany(IssueComment::class); }
}
