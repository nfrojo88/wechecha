<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakeoffItem extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'calculation_data' => 'array',
    ];

    public function sheet()
    {
        return $this->belongsTo(TakeoffSheet::class, 'takeoff_sheet_id');
    }

    public function section()
    {
        return $this->belongsTo(TakeoffSection::class, 'takeoff_section_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function rebarDetails()
    {
        return $this->hasMany(TakeoffRebarDetail::class);
    }
}
