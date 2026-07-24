<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TakeoffEditRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'takeoff_sheet_id',
        'user_id',
        'status',
    ];

    public function takeoffSheet()
    {
        return $this->belongsTo(TakeoffSheet::class, 'takeoff_sheet_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
