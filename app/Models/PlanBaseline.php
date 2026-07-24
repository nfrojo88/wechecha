<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanBaseline extends Model {
    use HasFactory;
    protected $guarded = ['id'];
    protected $casts = ['snapshot_data' => 'array'];
    public function header() { return $this->belongsTo(ErpPlanHeader::class, 'plan_header_id'); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
}
