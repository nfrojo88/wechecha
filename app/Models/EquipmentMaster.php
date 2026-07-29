<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentMaster extends Model
{
    protected $guarded = [];

    public function productivities()
    {
        return $this->hasMany(EquipmentProductivity::class, 'equipment_id');
    }

    // Individual fixed asset units linked to this equipment type
    public function fixedAssetUnits()
    {
        return $this->hasMany(EquipmentFixedAssetUnit::class, 'equipment_master_id');
    }

    // Count of available units
    public function getAvailableUnitsCountAttribute(): int
    {
        return $this->fixedAssetUnits()->where('status', 'available')->count();
    }

    // Total linked units count
    public function getTotalUnitsCountAttribute(): int
    {
        return $this->fixedAssetUnits()->count();
    }
}
