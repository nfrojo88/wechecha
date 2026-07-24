<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RebarDiaProduct extends Model
{
    protected $table    = 'rebar_dia_products';
    protected $fillable = ['diameter', 'unit_weight_kg_per_m', 'product_id', 'standard_length_m'];

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Standard diameters and their unit weights (kg/m)
     */
    public static function standardDiameters(): array
    {
        return [
            8  => 0.395,
            10 => 0.617,
            12 => 0.889,
            14 => 1.210,
            16 => 1.580,
            20 => 2.469,
            24 => 3.550,
            32 => 6.313,
        ];
    }

    /**
     * Get all diameters as a keyed collection: diameter => RebarDiaProduct
     */
    public static function allKeyed(): \Illuminate\Support\Collection
    {
        return self::with('product')->get()->keyBy('diameter');
    }
}
