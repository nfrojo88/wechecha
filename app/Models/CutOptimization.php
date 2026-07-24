<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CutOptimization extends Model
{
    protected $guarded = [];
    protected $casts = ['optimization_result' => 'array'];

    public function project() { return $this->belongsTo(Project::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
    public function items() { return $this->hasMany(CutOptimizationItem::class); }
}
