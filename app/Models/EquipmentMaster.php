<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentMaster extends Model
{
    protected $guarded = [];

    public function productivities() { return $this->hasMany(EquipmentProductivity::class, 'equipment_id'); }
}
