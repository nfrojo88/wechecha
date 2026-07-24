<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentProductivity extends Model
{
    protected $table = 'equipment_productivity';
    protected $guarded = [];
    protected $casts = ['work_date' => 'date'];

    public function equipment() { return $this->belongsTo(EquipmentMaster::class, 'equipment_id'); }
    public function project() { return $this->belongsTo(Project::class); }
    public function recordedBy() { return $this->belongsTo(User::class, 'recorded_by'); }
}
