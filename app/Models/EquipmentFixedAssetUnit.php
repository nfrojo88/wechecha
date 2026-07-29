<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EquipmentFixedAssetUnit extends Model
{
    protected $table = 'equipment_fixed_asset_units';
    protected $guarded = [];

    protected $casts = [
        'year' => 'integer',
    ];

    // The equipment type this unit belongs to
    public function equipmentMaster()
    {
        return $this->belongsTo(EquipmentMaster::class, 'equipment_master_id');
    }

    // Optional: linked product in the fixed asset product catalog
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Status badge color helper
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'available'   => 'success',
            'on_site'     => 'primary',
            'maintenance' => 'warning',
            'retired'     => 'secondary',
            default       => 'light',
        };
    }
}
